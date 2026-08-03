<?php

namespace App\Services;

use App\Models\CylinderType;
use App\Models\EmptyReturn;
use App\Models\EmptyReturnItem;
use App\Models\StockOutlet;
use Illuminate\Support\Facades\DB;
use App\Helpers\ReferenceGenerator;

class EmptyReturnService
{
    public function createReturn(int $outletId, array $items, ?string $notes = null, ?string $returnDate = null): EmptyReturn
    {
        return DB::transaction(function () use ($outletId, $items, $notes, $returnDate) {
            foreach ($items as $item) {
                $stockOutlet = StockOutlet::where('outlet_id', $outletId)
                    ->where('cylinder_type_id', $item['cylinder_type_id'])
                    ->first();
                $available = $stockOutlet ? $stockOutlet->empty_qty : 0;

                if ($item['quantity'] > $available) {
                    $cylinderType = CylinderType::find($item['cylinder_type_id']);
                    throw new \Exception("Insufficient empty cylinders at outlet for {$cylinderType->name}. Available: {$available}, Requested: {$item['quantity']}");
                }
            }

            $return = EmptyReturn::create([
                'return_number' => ReferenceGenerator::generateEmptyReturnNumber(),
                'outlet_id' => $outletId,
                'return_date' => $returnDate ?? now()->toDateString(),
                'notes' => $notes,
            ]);

            $stockService = app(StockService::class);

            foreach ($items as $item) {
                EmptyReturnItem::create([
                    'empty_return_id' => $return->id,
                    'cylinder_type_id' => $item['cylinder_type_id'],
                    'quantity' => $item['quantity'],
                ]);

                $stockService->updateOutletStock(
                    $outletId,
                    $item['cylinder_type_id'],
                    0,
                    -$item['quantity'],
                    'empty_return_out',
                    'EmptyReturn',
                    $return->id,
                    "Empty return to main store",
                    $return->return_date
                );

                $stockService->updateMainStock(
                    $item['cylinder_type_id'],
                    0,
                    $item['quantity'],
                    'empty_return_in',
                    'EmptyReturn',
                    $return->id,
                    "Empty return from outlet ID: {$outletId}",
                    $return->return_date
                );
            }

            $return->update(['status' => 'completed']);

            return $return;
        });
    }

    public function cancelReturn(EmptyReturn $return, string $reason): EmptyReturn
    {
        if ($return->status === 'cancelled') {
            throw new \Exception('This return is already cancelled.');
        }

        return DB::transaction(function () use ($return, $reason) {
            $return->load('items.cylinderType');
            $stockService = app(StockService::class);

            foreach ($return->items as $item) {
                $stockService->updateMainStock(
                    $item->cylinder_type_id,
                    0,
                    -$item->quantity,
                    'empty_return_cancel',
                    'EmptyReturn',
                    $return->id,
                    "Cancellation of Empty Return {$return->return_number}",
                    $return->return_date
                );

                $stockService->updateOutletStock(
                    $return->outlet_id,
                    $item->cylinder_type_id,
                    0,
                    $item->quantity,
                    'empty_return_cancel',
                    'EmptyReturn',
                    $return->id,
                    "Cancellation of Empty Return {$return->return_number}",
                    $return->return_date
                );
            }

            $return->update([
                'status' => 'cancelled',
                'notes' => trim(($return->notes ?? '')."\n[Cancelled]: ".$reason),
            ]);

            return $return;
        });
    }
}