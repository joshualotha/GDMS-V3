<?php

namespace App\Services;

use App\Helpers\ReferenceGenerator;
use App\Models\Accessory;
use App\Models\AccessoryStockLedger;
use App\Models\CylinderType;
use App\Models\Sale;
use App\Models\SaleAccessoryItem;
use App\Models\SaleItem;
use App\Models\StockMainLedger;
use App\Models\StockOutlet;
use App\Models\StockOutletAccessory;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function createSale(
        int $outletId,
        string $saleDate,
        array $items,
        array $accessoryItems = [],
        ?string $notes = null,
        ?float $cashSubmitted = null,
        ?string $receiptPath = null,
        ?int $submittedBy = null
    ): Sale {
        return DB::transaction(function () use ($outletId, $saleDate, $items, $accessoryItems, $notes, $cashSubmitted, $receiptPath, $submittedBy) {
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

                // Both 'full' and 'refill' sales hand the customer a full cylinder.
                // A 'refill' additionally takes the customer's empty cylinder into outlet stock.
                $fullChange = -$item['quantity'];
                $emptyChange = $item['sale_type'] == 'refill' ? $item['quantity'] : 0;

                $outletStock = StockOutlet::where('outlet_id', $outletId)
                    ->where('cylinder_type_id', $item['cylinder_type_id'])
                    ->first();
                $outletAvailable = $outletStock ? $outletStock->full_qty : 0;

                if ($item['quantity'] > $outletAvailable) {
                    throw new \Exception("Insufficient full cylinders for {$cylinderType->name}. Available: {$outletAvailable}, Requested: {$item['quantity']}");
                }

                // Only create outlet ledger, NOT main warehouse for sales
                $outletStock = StockOutlet::firstOrCreate(
                    ['outlet_id' => $outletId, 'cylinder_type_id' => $item['cylinder_type_id']],
                    ['full_qty' => 0, 'empty_qty' => 0]
                );

                if ($outletStock) {
                    $outletStock->decrement('full_qty', $item['quantity']);
                    if ($item['sale_type'] == 'refill') {
                        $outletStock->increment('empty_qty', $item['quantity']);
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

            foreach ($accessoryItems as $item) {
                $accessory = Accessory::find($item['accessory_id']);

                $unitPrice = floatval($accessory->sale_price);
                $unitCost = floatval($accessory->cost_price);
                $lineTotal = floatval($item['quantity']) * $unitPrice;
                $lineCost = floatval($item['quantity']) * $unitCost;

                SaleAccessoryItem::create([
                    'sale_id' => $sale->id,
                    'accessory_id' => $item['accessory_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'unit_cost' => $unitCost,
                    'total_price' => $lineTotal,
                    'total_cost' => $lineCost,
                    'gross_profit' => $lineTotal - $lineCost,
                ]);

                $totalPrice += $lineTotal;
                $totalCost += $lineCost;

                $outletStock = StockOutletAccessory::where('outlet_id', $outletId)
                    ->where('accessory_id', $item['accessory_id'])
                    ->first();
                $outletAvailable = $outletStock ? $outletStock->qty : 0;

                if ($item['quantity'] > $outletAvailable) {
                    throw new \Exception("Insufficient stock for {$accessory->name}. Available: {$outletAvailable}, Requested: {$item['quantity']}");
                }

                $outletStock = StockOutletAccessory::firstOrCreate(
                    ['outlet_id' => $outletId, 'accessory_id' => $item['accessory_id']],
                    ['qty' => 0]
                );
                $outletStock->decrement('qty', $item['quantity']);

                AccessoryStockLedger::create([
                    'accessory_id' => $item['accessory_id'],
                    'outlet_id' => $outletId,
                    'movement_date' => $saleDate,
                    'qty_change' => -$item['quantity'],
                    'qty_after' => $outletStock->qty,
                    'transaction_type' => 'sale',
                    'reference_type' => 'sale',
                    'reference_id' => $sale->id,
                    'note' => "Sale {$sale->sale_number} (Outlet)",
                ]);
            }

            $data = [
                'total_price' => floatval($totalPrice),
                'total_cost' => floatval($totalCost),
                'total_gross_profit' => floatval($totalPrice) - floatval($totalCost),
            ];

            // Recording the deposit + receipt at the same time as the sale is the normal
            // path (outlet already banked the cash before it gets entered) - no separate
            // approval click needed. Leave as 'pending' only if cash wasn't provided yet.
            if ($cashSubmitted !== null) {
                $data['cash_submitted'] = $cashSubmitted;
                $data['cash_submitted_date'] = now()->toDateString();
                $data['cash_submitted_by'] = $submittedBy;
                $data['cash_receipt_image'] = $receiptPath;
                $data['status'] = 'approved';
            }

            $sale->update($data);

            return $sale;
        });
    }

    public function cancelSale(Sale $sale, string $reason): Sale
    {
        if ($sale->status === 'cancelled') {
            throw new \Exception('This sale is already cancelled.');
        }

        return DB::transaction(function () use ($sale, $reason) {
            $sale->load('items.cylinderType', 'accessoryItems.accessory');

            foreach ($sale->items as $item) {
                $outletStock = StockOutlet::where('outlet_id', $sale->outlet_id)
                    ->where('cylinder_type_id', $item->cylinder_type_id)
                    ->first();

                if ($item->sale_type === 'refill' && $outletStock && $outletStock->empty_qty < $item->quantity) {
                    throw new \Exception("Cannot cancel: reversing this sale would take {$item->cylinderType->name} empty stock below 0 (likely already returned to main store).");
                }

                $outletStock = StockOutlet::firstOrCreate(
                    ['outlet_id' => $sale->outlet_id, 'cylinder_type_id' => $item->cylinder_type_id],
                    ['full_qty' => 0, 'empty_qty' => 0]
                );

                $outletStock->increment('full_qty', $item->quantity);
                if ($item->sale_type === 'refill') {
                    $outletStock->decrement('empty_qty', $item->quantity);
                }

                StockMainLedger::create([
                    'cylinder_type_id' => $item->cylinder_type_id,
                    'full_qty_change' => $item->quantity,
                    'empty_qty_change' => $item->sale_type === 'refill' ? -$item->quantity : 0,
                    'full_qty_after' => $outletStock->full_qty,
                    'empty_qty_after' => $outletStock->empty_qty,
                    'transaction_type' => 'sale_cancel',
                    'reference_type' => 'sale',
                    'reference_id' => $sale->id,
                    'note' => "Cancellation of Sale {$sale->sale_number}",
                    'outlet_id' => $sale->outlet_id,
                    'movement_date' => now()->toDateString(),
                ]);
            }

            foreach ($sale->accessoryItems as $item) {
                $outletStock = StockOutletAccessory::firstOrCreate(
                    ['outlet_id' => $sale->outlet_id, 'accessory_id' => $item->accessory_id],
                    ['qty' => 0]
                );

                $outletStock->increment('qty', $item->quantity);

                AccessoryStockLedger::create([
                    'accessory_id' => $item->accessory_id,
                    'outlet_id' => $sale->outlet_id,
                    'movement_date' => now()->toDateString(),
                    'qty_change' => $item->quantity,
                    'qty_after' => $outletStock->qty,
                    'transaction_type' => 'sale_cancel',
                    'reference_type' => 'sale',
                    'reference_id' => $sale->id,
                    'note' => "Cancellation of Sale {$sale->sale_number}",
                ]);
            }

            $sale->update([
                'status' => 'cancelled',
                'notes' => trim(($sale->notes ?? '')."\n[Cancelled]: ".$reason),
            ]);

            return $sale;
        });
    }
}
