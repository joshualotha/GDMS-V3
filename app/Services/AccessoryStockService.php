<?php

namespace App\Services;

use App\Models\AccessoryStockLedger;
use App\Models\StockMainAccessory;
use App\Models\StockOutletAccessory;
use Illuminate\Support\Facades\DB;

class AccessoryStockService
{
    public function updateMainStock(
        int $accessoryId,
        int $qtyChange,
        string $transactionType,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $note = null,
        ?string $movementDate = null
    ): void {
        DB::transaction(function () use ($accessoryId, $qtyChange, $transactionType, $referenceType, $referenceId, $note, $movementDate) {
            $stock = StockMainAccessory::firstOrCreate(
                ['accessory_id' => $accessoryId],
                ['qty' => 0]
            );

            $newQty = $stock->qty + $qtyChange;

            if ($newQty < 0) {
                throw new \Exception("Cannot reduce accessory stock below 0. Current: {$stock->qty}, Change: {$qtyChange}");
            }

            $stock->update(['qty' => $newQty]);

            AccessoryStockLedger::create([
                'accessory_id' => $accessoryId,
                'movement_date' => $movementDate ?? now()->toDateString(),
                'qty_change' => $qtyChange,
                'qty_after' => $newQty,
                'transaction_type' => $transactionType,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'note' => $note,
            ]);
        });
    }

    public function updateOutletStock(
        int $outletId,
        int $accessoryId,
        int $qtyChange,
        string $transactionType,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $note = null,
        ?string $movementDate = null
    ): void {
        DB::transaction(function () use ($outletId, $accessoryId, $qtyChange, $transactionType, $referenceType, $referenceId, $note, $movementDate) {
            $stock = StockOutletAccessory::firstOrCreate(
                ['outlet_id' => $outletId, 'accessory_id' => $accessoryId],
                ['qty' => 0]
            );

            $newQty = $stock->qty + $qtyChange;

            if ($newQty < 0) {
                throw new \Exception("Cannot reduce accessory stock below 0. Current: {$stock->qty}, Change: {$qtyChange}");
            }

            $stock->update(['qty' => $newQty]);

            AccessoryStockLedger::create([
                'accessory_id' => $accessoryId,
                'outlet_id' => $outletId,
                'movement_date' => $movementDate ?? now()->toDateString(),
                'qty_change' => $qtyChange,
                'qty_after' => $newQty,
                'transaction_type' => $transactionType,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'note' => $note,
            ]);
        });
    }
}
