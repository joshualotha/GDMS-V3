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
    public function createReturn(int $outletId, array $items, ?string $notes = null): EmptyReturn
    {
        return DB::transaction(function () use ($outletId, $items, $notes) {
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
                'notes' => $notes,
            ]);

            $stockService = app(StockService::class);

            foreach ($items as $item) {
                EmptyReturnItem::create([
                    'empty_return_id' => $return->id,
                    'cylinder_type_id' => $item['cylinder_type_id'],
                    'quantity' => $item['quantity'],
                ]);

                $stockOutlet = StockOutlet::where('outlet_id', $outletId)
                    ->where('cylinder_type_id', $item['cylinder_type_id'])
                    ->first();
                $stockOutlet->decrement('empty_qty', $item['quantity']);

                $stockService->updateMainStock(
                    $item['cylinder_type_id'],
                    0,
                    $item['quantity'],
                    'empty_return_in',
                    'EmptyReturn',
                    $return->id,
                    "Empty return from outlet ID: {$outletId}"
                );
            }

            $return->update(['status' => 'completed']);

            return $return;
        });
    }
}