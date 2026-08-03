<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FuelIssue;
use App\Models\FuelPurchase;
use App\Models\FuelStock;
use App\Models\Outlet;
use Illuminate\Support\Facades\DB;

class FuelService
{
    public function recordPurchase(array $data): FuelPurchase
    {
        return DB::transaction(function () use ($data) {
            $purchase = FuelPurchase::create($data);

            // Legacy bulk (warehouse-pool) purchases have no outlet_id and still feed FuelStock.
            // Purchases made at the pump for a specific vehicle bypass the pool entirely.
            $outlet = null;
            if (empty($data['outlet_id'])) {
                FuelStock::updateOrCreate(
                    ['fuel_type' => $data['fuel_type']],
                    []
                )->increment('litres', $data['litres']);
            } else {
                $outlet = Outlet::find($data['outlet_id']);
            }

            $fuelCategory = ExpenseCategory::firstOrCreate(
                ['name' => 'Fuel'],
                ['is_active' => true]
            );

            $description = $outlet
                ? "Fuel Purchase - {$outlet->name} - ".ucfirst($data['fuel_type']).' ('.$data['litres'].'L)'
                : 'Fuel Purchase - '.ucfirst($data['fuel_type']).' ('.$data['litres'].'L)';

            Expense::create([
                'expense_number' => 'EXP-'.date('Ymd').'-'.str_pad(Expense::count() + 1, 4, '0', STR_PAD_LEFT),
                'expense_category_id' => $fuelCategory->id,
                'asset_id' => $outlet->asset_id ?? null,
                'fuel_purchase_id' => $purchase->id,
                'expense_date' => $data['date'] ?? today(),
                'description' => $description,
                'amount' => $purchase->total_cost,
                'reference' => $purchase->receipt_number,
            ]);

            return $purchase;
        });
    }

    public function deletePurchase(FuelPurchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            if (! $purchase->outlet_id) {
                $fuelStock = FuelStock::where('fuel_type', $purchase->fuel_type)->lockForUpdate()->first();
                $available = $fuelStock ? $fuelStock->litres : 0;

                if ($purchase->litres > $available) {
                    throw new \Exception("Cannot delete: this would take {$purchase->fuel_type} stock below 0 (some of this fuel has likely already been issued). Available: {$available}L, purchase: {$purchase->litres}L.");
                }

                $fuelStock->decrement('litres', $purchase->litres);
            }

            Expense::where('fuel_purchase_id', $purchase->id)->delete();

            $purchase->delete();
        });
    }

    public function deleteIssue(FuelIssue $issue): void
    {
        DB::transaction(function () use ($issue) {
            FuelStock::updateOrCreate(
                ['fuel_type' => $issue->fuel_type],
                []
            )->increment('litres', $issue->litres);

            $issue->delete();
        });
    }

    public function issueFuel(array $data): FuelIssue
    {
        return DB::transaction(function () use ($data) {
            $fuelStock = FuelStock::where('fuel_type', $data['fuel_type'])->lockForUpdate()->first();
            $available = $fuelStock ? $fuelStock->litres : 0;

            if ($data['litres'] > $available) {
                throw new \Exception("Insufficient fuel. Available: {$available}L, Requested: {$data['litres']}L");
            }

            $issue = FuelIssue::create($data);
            $fuelStock->decrement('litres', $data['litres']);

            return $issue;
        });
    }
}
