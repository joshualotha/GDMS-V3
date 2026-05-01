<?php

namespace App\Services;

use App\Models\StockMain;
use App\Models\StockMainLedger;
use App\Models\StockOutlet;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function updateMainStock(
        int $cylinderTypeId,
        int $fullQtyChange,
        int $emptyQtyChange,
        string $transactionType,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $note = null
    ): void {
        DB::transaction(function () use ($cylinderTypeId, $fullQtyChange, $emptyQtyChange, $transactionType, $referenceType, $referenceId, $note) {
            $stockMain = StockMain::firstOrCreate(
                ['cylinder_type_id' => $cylinderTypeId],
                ['full_qty' => 0, 'empty_qty' => 0]
            );

            $newFullQty = $stockMain->full_qty + $fullQtyChange;
            $newEmptyQty = $stockMain->empty_qty + $emptyQtyChange;

            if ($newFullQty < 0) {
                throw new \Exception("Cannot reduce full_qty below 0. Current: {$stockMain->full_qty}, Change: {$fullQtyChange}");
            }

            if ($newEmptyQty < 0) {
                throw new \Exception("Cannot reduce empty_qty below 0. Current: {$stockMain->empty_qty}, Change: {$emptyQtyChange}");
            }

            $stockMain->update([
                'full_qty' => $newFullQty,
                'empty_qty' => $newEmptyQty,
            ]);

            StockMainLedger::create([
                'cylinder_type_id' => $cylinderTypeId,
                'full_qty_change' => $fullQtyChange,
                'empty_qty_change' => $emptyQtyChange,
                'full_qty_after' => $newFullQty,
                'empty_qty_after' => $newEmptyQty,
                'transaction_type' => $transactionType,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'note' => $note,
            ]);
        });
    }

    public function updateOutletStock(
        int $outletId,
        int $cylinderTypeId,
        int $fullQtyChange,
        int $emptyQtyChange,
        string $transactionType,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $note = null
    ): void {
        DB::transaction(function () use ($outletId, $cylinderTypeId, $fullQtyChange, $emptyQtyChange, $transactionType, $referenceType, $referenceId, $note) {
            $stockOutlet = StockOutlet::firstOrCreate(
                ['outlet_id' => $outletId, 'cylinder_type_id' => $cylinderTypeId],
                ['full_qty' => 0, 'empty_qty' => 0]
            );

            $newFullQty = $stockOutlet->full_qty + $fullQtyChange;
            $newEmptyQty = $stockOutlet->empty_qty + $emptyQtyChange;

            if ($newFullQty < 0) {
                throw new \Exception("Cannot reduce full_qty below 0. Current: {$stockOutlet->full_qty}, Change: {$fullQtyChange}");
            }

            if ($newEmptyQty < 0) {
                throw new \Exception("Cannot reduce empty_qty below 0. Current: {$stockOutlet->empty_qty}, Change: {$emptyQtyChange}");
            }

            $stockOutlet->update([
                'full_qty' => $newFullQty,
                'empty_qty' => $newEmptyQty,
            ]);

            StockMainLedger::create([
                'outlet_id' => $outletId,
                'cylinder_type_id' => $cylinderTypeId,
                'full_qty_change' => $fullQtyChange,
                'empty_qty_change' => $emptyQtyChange,
                'full_qty_after' => $newFullQty,
                'empty_qty_after' => $newEmptyQty,
                'transaction_type' => $transactionType,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'note' => $note,
            ]);
        });
    }
}
