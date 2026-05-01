<?php

namespace App\Services;

use App\Helpers\ReferenceGenerator;
use App\Models\CylinderType;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMain;
use App\Models\StockMainLedger;
use App\Models\StockOutlet;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function createSale(int $outletId, string $saleDate, array $items, ?string $notes = null): Sale
    {
        return DB::transaction(function () use ($outletId, $saleDate, $items, $notes) {
            $sale = Sale::create([
                'sale_number' => ReferenceGenerator::generateSaleNumber(),
                'outlet_id' => $outletId,
                'sale_date' => $saleDate,
                'notes' => $notes,
            ]);

            $totalPrice = 0;
            $totalCost = 0;

            foreach ($items as $item) {
                $cylinderType = CylinderType::find($item['cylinder_type_id']);

                $unitPrice = $item['sale_type'] == 'full'
                    ? floatval($cylinderType->full_sale_price)
                    : floatval($cylinderType->refill_price);
                $unitCost = $item['sale_type'] == 'full'
                    ? floatval($cylinderType->full_sale_cost)
                    : floatval($cylinderType->refill_cost);

                $lineTotal = floatval($item['quantity']) * $unitPrice;
                $lineCost = floatval($item['quantity']) * $unitCost;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'cylinder_type_id' => $item['cylinder_type_id'],
                    'sale_type' => $item['sale_type'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'unit_cost' => $unitCost,
                    'total_price' => $lineTotal,
                    'total_cost' => $lineCost,
                    'gross_profit' => $lineTotal - $lineCost,
                ]);

                $totalPrice += $lineTotal;
                $totalCost += $lineCost;

                $fullChange = $item['sale_type'] == 'full' ? -$item['quantity'] : 0;
                $emptyChange = $item['sale_type'] == 'refill' ? $item['quantity'] : 0;

                $outletStock = StockOutlet::where('outlet_id', $outletId)
                    ->where('cylinder_type_id', $item['cylinder_type_id'])
                    ->first();
                $outletAvailable = $outletStock ? $outletStock->full_qty : 0;

                if ($item['sale_type'] == 'full' && $item['quantity'] > $outletAvailable) {
                    throw new \Exception("Insufficient stock for {$cylinderType->name}. Available: {$outletAvailable}, Requested: {$item['quantity']}");
                }

                if ($item['sale_type'] == 'refill') {
                    $emptyAvailable = $outletStock ? $outletStock->empty_qty : 0;
                    if ($item['quantity'] > $emptyAvailable) {
                        throw new \Exception("Insufficient empty cylinders for {$cylinderType->name} refill. Available empties: {$emptyAvailable}, Requested: {$item['quantity']}");
                    }
                }

                $mainStock = StockMain::where('cylinder_type_id', $item['cylinder_type_id'])->first();
                $fullAfter = $mainStock ? $mainStock->full_qty + $fullChange : $fullChange;
                $emptyAfter = $mainStock ? $mainStock->empty_qty + $emptyChange : $emptyChange;

                // Only create outlet ledger, NOT main warehouse for sales
                $outletStock = StockOutlet::firstOrCreate(
                    ['outlet_id' => $outletId, 'cylinder_type_id' => $item['cylinder_type_id']],
                    ['full_qty' => 0, 'empty_qty' => 0]
                );

                if ($outletStock) {
                    if ($item['sale_type'] == 'full') {
                        $outletStock->decrement('full_qty', $item['quantity']);
                    } else {
                        $outletStock->decrement('empty_qty', $item['quantity']);
                    }
                    StockMainLedger::create([
                        'cylinder_type_id' => $item['cylinder_type_id'],
                        'full_qty_change' => $fullChange,
                        'empty_qty_change' => $emptyChange,
                        'full_qty_after' => $outletStock->full_qty,
                        'empty_qty_after' => $outletStock->empty_qty,
                        'transaction_type' => 'sale',
                        'reference_type' => 'sale',
                        'reference_id' => $sale->id,
                        'note' => "Sale {$sale->sale_number} - {$item['sale_type']} (Outlet)",
                        'outlet_id' => $outletId,
                    ]);
                }
            }

            $sale->update([
                'total_price' => floatval($totalPrice),
                'total_cost' => floatval($totalCost),
                'total_gross_profit' => floatval($totalPrice) - floatval($totalCost),
            ]);

            return $sale;
        });
    }
}
