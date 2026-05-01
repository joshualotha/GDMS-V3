<?php

namespace App\Services;

use App\Models\CylinderType;
use App\Models\StockMain;
use App\Models\StockOutlet;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use Illuminate\Support\Facades\DB;
use App\Helpers\ReferenceGenerator;

class TransferService
{
    public function createTransfer(int $outletId, array $items, ?string $notes = null): StockTransfer
    {
        return DB::transaction(function () use ($outletId, $items, $notes) {
            // Validate stock availability first
            foreach ($items as $item) {
                $stockMain = StockMain::where('cylinder_type_id', $item['cylinder_type_id'])->first();
                $available = $stockMain ? $stockMain->full_qty : 0;

                if ($item['quantity'] > $available) {
                    $cylinderType = CylinderType::find($item['cylinder_type_id']);
                    throw new \Exception("Insufficient stock for {$cylinderType->name}. Available: {$available}, Requested: {$item['quantity']}");
                }
            }

            // Create transfer
            $transfer = StockTransfer::create([
                'transfer_number' => ReferenceGenerator::generateTransferNumber(),
                'outlet_id' => $outletId,
                'notes' => $notes,
            ]);

            foreach ($items as $item) {
                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'cylinder_type_id' => $item['cylinder_type_id'],
                    'quantity' => $item['quantity'],
                ]);

                // Deduct from main store
                $stockService = app(StockService::class);
                $stockService->updateMainStock(
                    $item['cylinder_type_id'],
                    -$item['quantity'], // -full (going out)
                    0,
                    'transfer_out',
                    'StockTransfer',
                    $transfer->id,
                    "Transfer to outlet ID: {$outletId}"
                );

                // Add to outlet stock
                StockOutlet::updateOrCreate(
                    [
                        'outlet_id' => $outletId,
                        'cylinder_type_id' => $item['cylinder_type_id'],
                    ],
                    []
                )->increment('full_qty', $item['quantity']);
            }

            $transfer->update(['status' => 'completed']);

            return $transfer;
        });
    }
}