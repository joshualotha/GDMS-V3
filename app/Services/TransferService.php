<?php

namespace App\Services;

use App\Models\CylinderType;
use App\Models\StockMain;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use Illuminate\Support\Facades\DB;
use App\Helpers\ReferenceGenerator;

class TransferService
{
    public function createTransfer(int $outletId, array $items, ?string $notes = null, ?string $transferDate = null): StockTransfer
    {
        return DB::transaction(function () use ($outletId, $items, $notes, $transferDate) {
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
                'transfer_date' => $transferDate ?? now()->toDateString(),
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
                    "Transfer to outlet ID: {$outletId}",
                    $transfer->transfer_date
                );

                // Add to outlet stock (also records the outlet-side ledger entry)
                $stockService->updateOutletStock(
                    $outletId,
                    $item['cylinder_type_id'],
                    $item['quantity'],
                    0,
                    'transfer_in',
                    'StockTransfer',
                    $transfer->id,
                    "Transfer from main store",
                    $transfer->transfer_date
                );
            }

            $transfer->update(['status' => 'completed']);

            return $transfer;
        });
    }

    public function cancelTransfer(StockTransfer $transfer, string $reason): StockTransfer
    {
        if ($transfer->status === 'cancelled') {
            throw new \Exception('This transfer is already cancelled.');
        }

        return DB::transaction(function () use ($transfer, $reason) {
            $transfer->load('items.cylinderType');
            $stockService = app(StockService::class);

            foreach ($transfer->items as $item) {
                $stockService->updateOutletStock(
                    $transfer->outlet_id,
                    $item->cylinder_type_id,
                    -$item->quantity,
                    0,
                    'transfer_cancel',
                    'StockTransfer',
                    $transfer->id,
                    "Cancellation of Transfer {$transfer->transfer_number}",
                    $transfer->transfer_date
                );

                $stockService->updateMainStock(
                    $item->cylinder_type_id,
                    $item->quantity,
                    0,
                    'transfer_cancel',
                    'StockTransfer',
                    $transfer->id,
                    "Cancellation of Transfer {$transfer->transfer_number}",
                    $transfer->transfer_date
                );
            }

            $transfer->update([
                'status' => 'cancelled',
                'notes' => trim(($transfer->notes ?? '')."\n[Cancelled]: ".$reason),
            ]);

            return $transfer;
        });
    }
}