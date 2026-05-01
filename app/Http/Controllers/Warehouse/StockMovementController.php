<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\CylinderType;
use App\Models\EmptyReturn;
use App\Models\Outlet;
use App\Models\StockMain;
use App\Models\StockMainLedger;
use App\Models\StockOutlet;
use App\Models\StockTransfer;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'transfer');

        if ($type == 'return') {
            $movements = EmptyReturn::with('outlet', 'items.cylinderType')
                ->orderBy('created_at', 'desc')
                ->get();
            $view = 'warehouse.movements.returns-list';
        } else {
            $movements = StockTransfer::with('outlet', 'items.cylinderType')
                ->orderBy('created_at', 'desc')
                ->get();
            $view = 'warehouse.movements.transfers-list';
        }

        return view('warehouse.movements.index', compact('movements', 'type'));
    }

    public function create(Request $request)
    {
        $type = $request->get('type', 'transfer');

        $outlets = Outlet::where('is_active', true)->orderBy('name')->get();
        $cylinderTypes = CylinderType::where('is_active', true)->orderBy('size_kg')->get();

        $stockMain = [];
        foreach (StockMain::all() as $s) {
            $stockMain[$s->cylinder_type_id] = $s;
        }

        return view('warehouse.movements.create', [
            'outlets' => $outlets,
            'cylinderTypes' => $cylinderTypes,
            'stockMain' => $stockMain,
            'type' => $type,
        ]);
    }

    public function store(Request $request)
    {
        $type = $request->get('type', 'transfer');

        $validated = $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.cylinder_type_id' => 'required|exists:cylinder_types,id',
            'items.*.quantity' => 'required|integer|min:0',
        ]);

        // Filter items with quantity > 0
        $items = array_filter($validated['items'], function ($item) {
            return isset($item['quantity']) && $item['quantity'] > 0;
        });

        if (empty($items)) {
            return back()->with('error', 'Please enter at least one quantity greater than 0.');
        }

        try {
            if ($type == 'return') {
                $this->createEmptyReturn($validated['outlet_id'], $items, $validated['notes'] ?? null);

                return redirect()->route('warehouse.movements', ['type' => 'return'])
                    ->with('success', 'Empty return recorded successfully.');
            } else {
                $this->createTransfer($validated['outlet_id'], $items, $validated['notes'] ?? null);

                return redirect()->route('warehouse.movements', ['type' => 'transfer'])
                    ->with('success', 'Transfer completed successfully.');
            }
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    protected function createTransfer(int $outletId, array $items, ?string $notes = null)
    {
        $outlet = Outlet::find($outletId);
        $transfer = StockTransfer::create([
            'transfer_number' => 'TRF-'.date('Ymd').'-'.str_pad(StockTransfer::count() + 1, 4, '0', STR_PAD_LEFT),
            'outlet_id' => $outletId,
            'status' => 'completed',
            'notes' => $notes,
        ]);

        foreach ($items as $item) {
            $transfer->items()->create([
                'cylinder_type_id' => $item['cylinder_type_id'],
                'quantity' => $item['quantity'],
            ]);

            $stockMain = StockMain::where('cylinder_type_id', $item['cylinder_type_id'])->first();
            if ($stockMain) {
                $stockMain->decrement('full_qty', $item['quantity']);
            }

            StockOutlet::updateOrCreate(
                ['outlet_id' => $outletId, 'cylinder_type_id' => $item['cylinder_type_id']],
                []
            )->increment('full_qty', $item['quantity']);

            StockMainLedger::create([
                'cylinder_type_id' => $item['cylinder_type_id'],
                'full_qty_change' => -$item['quantity'],
                'empty_qty_change' => 0,
                'full_qty_after' => $stockMain ? $stockMain->full_qty - $item['quantity'] : -$item['quantity'],
                'empty_qty_after' => $stockMain ? $stockMain->empty_qty : 0,
                'transaction_type' => 'transfer_out',
                'reference_type' => 'transfer',
                'reference_id' => $transfer->id,
                'note' => "Transfer to {$outlet->name}",
                'outlet_id' => $outletId,
            ]);
        }
    }

    protected function createEmptyReturn(int $outletId, array $items, ?string $notes = null)
    {
        $outlet = Outlet::find($outletId);
        $return = EmptyReturn::create([
            'return_number' => 'RET-'.date('Ymd').'-'.str_pad(EmptyReturn::count() + 1, 4, '0', STR_PAD_LEFT),
            'outlet_id' => $outletId,
            'status' => 'completed',
            'notes' => $notes,
        ]);

        foreach ($items as $item) {
            $return->items()->create([
                'cylinder_type_id' => $item['cylinder_type_id'],
                'quantity' => $item['quantity'],
            ]);

            $outletStock = StockOutlet::where('outlet_id', $outletId)
                ->where('cylinder_type_id', $item['cylinder_type_id'])
                ->first();

            if ($outletStock) {
                $outletStock->decrement('empty_qty', $item['quantity']);
                $outletStock->increment('full_qty', $item['quantity']);
            }

            $stockMain = StockMain::where('cylinder_type_id', $item['cylinder_type_id'])->first();
            if ($stockMain) {
                $stockMain->increment('empty_qty', $item['quantity']);
            }

            StockMainLedger::create([
                'cylinder_type_id' => $item['cylinder_type_id'],
                'full_qty_change' => $item['quantity'],
                'empty_qty_change' => -$item['quantity'],
                'full_qty_after' => $stockMain ? $stockMain->full_qty + $item['quantity'] : $item['quantity'],
                'empty_qty_after' => $stockMain ? $stockMain->empty_qty - $item['quantity'] : -$item['quantity'],
                'transaction_type' => 'empty_return_in',
                'reference_type' => 'empty_return',
                'reference_id' => $return->id,
                'note' => "Empty return from {$outlet->name}",
                'outlet_id' => $outletId,
            ]);
        }
    }

    public function getStock($outletId)
    {
        $stock = StockOutlet::where('outlet_id', $outletId)->get()->keyBy('cylinder_type_id');

        return response()->json($stock->toArray());
    }
}
