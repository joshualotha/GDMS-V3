<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FuelIssue;
use App\Models\FuelPurchase;
use App\Models\Outlet;
use Illuminate\Support\Facades\DB;

class FuelService
{
    public function recordPurchase(array $data): FuelPurchase
    {
        return DB::transaction(function () use ($data) {
            $purchase = FuelPurchase::create($data);

            $outlet = Outlet::find($data['outlet_id']);

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
            Expense::where('fuel_purchase_id', $purchase->id)->delete();

            $purchase->delete();
        });
    }

    public function deleteIssue(FuelIssue $issue): void
    {
        $issue->delete();
    }
}
