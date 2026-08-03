<?php

namespace App\Services;

use App\Helpers\ReferenceGenerator;
use App\Models\Accessory;
use App\Models\AccessoryTransfer;
use App\Models\AccessoryTransferItem;
use App\Models\StockMainAccessory;
use Illuminate\Support\Facades\DB;

class AccessoryTransferService
{
    public function createTransfer(int $outletId, array $items, ?string $notes = null, ?string $transferDate = null): AccessoryTransfer
    {
        return DB::transaction(function () use ($outletId, $items, $notes, $transferDate) {
            foreach ($items as $item) {
                $stockMain = StockMainAccessory::where('accessory_id', $item['accessory_id'])->first();
                $available = $stockMain ? $stockMain->qty : 0;

                if ($item['quantity'] > $available) {
                    $accessory = Accessory::find($item['accessory_id']);
                    throw new \Exception("Insufficient stock for {$accessory->name}. Available: {$available}, Requested: {$item['quantity']}");
                }
            }

            $transfer = AccessoryTransfer::create([
                'transfer_number' => ReferenceGenerator::generateAccessoryTransferNumber(),
                'outlet_id' => $outletId,
                'transfer_date' => $transferDate ?? now()->toDateString(),
                'notes' => $notes,
            ]);

            $stockService = app(AccessoryStockService::class);

            foreach ($items as $item) {
                AccessoryTransferItem::create([
                    'accessory_transfer_id' => $transfer->id,
                    'accessory_id' => $item['accessory_id'],
                    'quantity' => $item['quantity'],
                ]);

                $stockService->updateMainStock(
                    $item['accessory_id'],
                    -$item['quantity'],
                    'accessory_transfer_out',
                    'AccessoryTransfer',
                    $transfer->id,
                    "Transfer to outlet ID: {$outletId}",
                    $transfer->transfer_date->toDateString()
                );

                $stockService->updateOutletStock(
                    $outletId,
                    $item['accessory_id'],
                    $item['quantity'],
                    'accessory_transfer_in',
                    'AccessoryTransfer',
                    $transfer->id,
                    "Transfer from main store",
                    $transfer->transfer_date->toDateString()
                );
            }

            $transfer->update(['status' => 'completed']);

            return $transfer;
        });
    }

    public function cancelTransfer(AccessoryTransfer $transfer, string $reason): AccessoryTransfer
    {
        if ($transfer->status === 'cancelled') {
            throw new \Exception('This transfer is already cancelled.');
        }

        return DB::transaction(function () use ($transfer, $reason) {
            $transfer->load('items.accessory');
            $stockService = app(AccessoryStockService::class);

            foreach ($transfer->items as $item) {
                $stockService->updateOutletStock(
                    $transfer->outlet_id,
                    $item->accessory_id,
                    -$item->quantity,
                    'accessory_transfer_cancel',
                    'AccessoryTransfer',
                    $transfer->id,
                    "Cancellation of Transfer {$transfer->transfer_number}",
                    $transfer->transfer_date->toDateString()
                );

                $stockService->updateMainStock(
                    $item->accessory_id,
                    $item->quantity,
                    'accessory_transfer_cancel',
                    'AccessoryTransfer',
                    $transfer->id,
                    "Cancellation of Transfer {$transfer->transfer_number}",
                    $transfer->transfer_date->toDateString()
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
