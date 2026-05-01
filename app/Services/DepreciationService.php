<?php

namespace App\Services;

use App\Models\CompanyAsset;
use App\Models\DepreciationLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DepreciationService
{
    /**
     * Convert annual depreciation rate to monthly rate.
     * Uses the reducing balance formula: monthly_rate = 1 - (1 - annual_rate)^(1/12)
     */
    protected function monthlyRate(float $annualRate): float
    {
        return (1 - pow(1 - ($annualRate / 100), 1 / 12)) * 100;
    }

    /**
     * Run depreciation for the current month (one period forward).
     * Only processes assets that haven't been depreciated this period yet.
     */
    public function runReducingBalance(?int $assetId = null): array
    {
        $results = [];

        $query = CompanyAsset::where('status', 'active')
            ->where('depreciation_rate', '>', 0)
            ->where('current_book_value', '>', 0);

        if ($assetId) {
            $query->where('id', $assetId);
        }

        $assets = $query->get();

        foreach ($assets as $asset) {
            // Skip if already depreciated this month
            $lastLog = $asset->depreciationLogs()->latest('period_end')->first();
            $currentPeriodStart = now()->startOfMonth()->toDateString();
            if ($lastLog && $lastLog->period_end >= $currentPeriodStart) {
                continue;
            }

            $monthlyRate = $this->monthlyRate($asset->depreciation_rate);
            $depreciationAmount = $asset->current_book_value * ($monthlyRate / 100);
            $newBookValue = $asset->current_book_value - $depreciationAmount;
            $newAccumulated = $asset->accumulated_depreciation + $depreciationAmount;

            if ($newBookValue < 0) {
                $depreciationAmount = $asset->current_book_value;
                $newBookValue = 0;
                $newAccumulated = $asset->purchase_cost;
            }

            DB::transaction(function () use ($asset, $depreciationAmount, $newBookValue, $newAccumulated) {
                $asset->update([
                    'accumulated_depreciation' => $newAccumulated,
                    'current_book_value' => $newBookValue,
                ]);

                DepreciationLog::create([
                    'asset_id' => $asset->id,
                    'period_start' => now()->startOfMonth()->toDateString(),
                    'period_end' => now()->endOfMonth()->toDateString(),
                    'depreciation_amount' => $depreciationAmount,
                    'book_value_before' => $asset->getOriginal('current_book_value'),
                    'depreciation_rate' => $asset->depreciation_rate,
                    'book_value_after' => $newBookValue,
                    'run_by' => auth()->user()->name ?? 'System',
                ]);
            });

            $results[] = [
                'asset_id' => $asset->id,
                'asset_number' => $asset->asset_number,
                'name' => $asset->name,
                'depreciation' => $depreciationAmount,
                'new_book_value' => $newBookValue,
            ];
        }

        return $results;
    }

    /**
     * Catch up depreciation from the last logged period (or purchase date) to now.
     * This processes all missed months in one go and creates log entries for each.
     */
    public function catchUpDepreciation(?int $assetId = null): array
    {
        $results = [];

        $query = CompanyAsset::where('status', 'active')
            ->where('depreciation_rate', '>', 0)
            ->where('current_book_value', '>', 0);

        if ($assetId) {
            $query->where('id', $assetId);
        }

        $assets = $query->get();

        foreach ($assets as $asset) {
            $lastLog = $asset->depreciationLogs()->latest('period_end')->first();
            $purchaseDate = $asset->purchase_date ? Carbon::parse($asset->purchase_date) : null;

            // Determine the starting month for catch-up
            if ($lastLog) {
                $startMonth = Carbon::parse($lastLog->period_end)->addMonth()->startOfMonth();
            } elseif ($purchaseDate) {
                $startMonth = $purchaseDate->copy()->startOfMonth()->addMonth();
            } else {
                continue; // No purchase date and no logs — can't determine start
            }

            $now = now()->startOfMonth();
            if ($startMonth->greaterThanOrEqualTo($now)) {
                continue; // Already caught up
            }

            $bookValue = $asset->current_book_value;
            $accumulated = $asset->accumulated_depreciation;
            $annualRate = $asset->depreciation_rate;
            $monthlyRate = $this->monthlyRate($annualRate);
            $assetResults = [];

            while ($startMonth->lessThanOrEqualTo($now)) {
                $periodStart = $startMonth->copy()->startOfMonth()->toDateString();
                $periodEnd = $startMonth->copy()->endOfMonth()->toDateString();

                $depreciationAmount = $bookValue * ($monthlyRate / 100);
                $bookValueBefore = $bookValue;
                $bookValue -= $depreciationAmount;
                $accumulated += $depreciationAmount;

                if ($bookValue < 0) {
                    $depreciationAmount = $bookValueBefore;
                    $bookValue = 0;
                    $accumulated = $asset->purchase_cost;
                }

                $currentBookValue = $bookValue;

                DB::transaction(function () use ($asset, $depreciationAmount, $currentBookValue, $accumulated, $periodStart, $periodEnd, $annualRate, $bookValueBefore) {
                    DepreciationLog::create([
                        'asset_id' => $asset->id,
                        'period_start' => $periodStart,
                        'period_end' => $periodEnd,
                        'depreciation_amount' => $depreciationAmount,
                        'book_value_before' => $bookValueBefore,
                        'depreciation_rate' => $annualRate,
                        'book_value_after' => $currentBookValue,
                        'run_by' => auth()->user()->name ?? 'System',
                    ]);
                });

                $assetResults[] = [
                    'period' => $periodStart . ' to ' . $periodEnd,
                    'depreciation' => $depreciationAmount,
                    'book_value_after' => $currentBookValue,
                ];

                if ($bookValue <= 0) {
                    break;
                }

                $startMonth->addMonth();
            }

            // Update the asset's final values
            $asset->update([
                'accumulated_depreciation' => $accumulated,
                'current_book_value' => $bookValue,
            ]);

            $results[] = [
                'asset_id' => $asset->id,
                'asset_number' => $asset->asset_number,
                'name' => $asset->name,
                'months_caught_up' => count($assetResults),
                'total_depreciation' => array_sum(array_column($assetResults, 'depreciation')),
                'final_book_value' => $bookValue,
            ];
        }

        return $results;
    }

    /**
     * Calculate depreciation for a single period (preview, no save).
     */
    public function calculateDepreciation(CompanyAsset $asset): array
    {
        if (!$asset->depreciation_rate || $asset->depreciation_rate <= 0) {
            return ['error' => 'No depreciation rate set'];
        }

        $monthlyRate = $this->monthlyRate($asset->depreciation_rate);
        $monthlyDepreciation = $asset->current_book_value * ($monthlyRate / 100);

        return [
            'annual_rate' => $asset->depreciation_rate . '%',
            'monthly_depreciation' => $monthlyDepreciation,
            'current_book_value' => $asset->current_book_value,
            'accumulated_depreciation' => $asset->accumulated_depreciation,
            'purchase_cost' => $asset->purchase_cost,
        ];
    }
}
