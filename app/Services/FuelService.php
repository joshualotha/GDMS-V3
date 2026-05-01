<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FuelIssue;
use App\Models\FuelPurchase;
use App\Models\FuelStock;
use Illuminate\Support\Facades\DB;

class FuelService
{
    public function recordPurchase(array $data): FuelPurchase
    {
        return DB::transaction(function () use ($data) {
            $purchase = FuelPurchase::create($data);

            FuelStock::updateOrCreate(
                ['fuel_type' => $data['fuel_type']],
                []
            )->increment('litres', $data['litres']);

            // Create expense entry for fuel purchase
            $fuelCategory = ExpenseCategory::firstOrCreate(
                ['name' => 'Fuel'],
                ['is_active' => true]
            );

            Expense::create([
                'expense_number' => 'EXP-'.date('Ymd').'-'.str_pad(Expense::count() + 1, 4, '0', STR_PAD_LEFT),
                'expense_category_id' => $fuelCategory->id,
                'expense_date' => $data['date'] ?? today(),
                'description' => 'Fuel Purchase - '.ucfirst($data['fuel_type']).' ('.$data['litres'].'L)',
                'amount' => $data['total_cost'],
                'reference' => $purchase->purchase_number,
            ]);

            return $purchase;
        });
    }

    public function issueFuel(array $data): FuelIssue
    {
        return DB::transaction(function () use ($data) {
            $fuelStock = FuelStock::where('fuel_type', $data['fuel_type'])->first();
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
