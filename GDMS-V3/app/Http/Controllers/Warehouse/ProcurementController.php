<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\CylinderType;
use App\Models\GoodsReceived;
use App\Models\StockMain;
use App\Models\StockMainLedger;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ProcurementController extends Controller
{
    public function index(Request $request)
    {
        $procurements = GoodsReceived::with('supplier')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('warehouse.procurement.index', compact('procurements'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $cylinderTypes = CylinderType::where('is_active', true)->orderBy('size_kg')->get();
        $stockMain = StockMain::all()->keyBy('cylinder_type_id');

        return view('warehouse.procurement.create', compact('suppliers', 'cylinderTypes', 'stockMain'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.cylinder_type_id' => 'required|exists:cylinder_types,id',
            'items.*.purchase_type' => 'required|in:full,refill',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        $grn = GoodsReceived::create([
            'grn_number' => 'GRN-'.date('Ymd').'-'.str_pad(GoodsReceived::count() + 1, 4, '0', STR_PAD_LEFT),
            'supplier_id' => $validated['supplier_id'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'completed',
        ]);

        $totalCost = 0;
        $supplier = Supplier::find($validated['supplier_id']);

        foreach ($validated['items'] as $item) {
            $lineTotal = $item['quantity'] * $item['unit_cost'];
            $grn->items()->create([
                'cylinder_type_id' => $item['cylinder_type_id'],
                'purchase_type' => $item['purchase_type'],
                'quantity' => $item['quantity'],
                'unit_cost' => $item['unit_cost'],
                'line_total' => $lineTotal,
            ]);
            $totalCost += $lineTotal;

            $stockMain = StockMain::firstOrCreate(
                ['cylinder_type_id' => $item['cylinder_type_id']],
                ['full_qty' => 0, 'empty_qty' => 0]
            );

            if ($item['purchase_type'] == 'full') {
                $stockMain->increment('full_qty', $item['quantity']);

                StockMainLedger::create([
                    'cylinder_type_id' => $item['cylinder_type_id'],
                    'full_qty_change' => $item['quantity'],
                    'empty_qty_change' => 0,
                    'full_qty_after' => $stockMain->full_qty,
                    'empty_qty_after' => $stockMain->empty_qty,
                    'transaction_type' => 'grn_full',
                    'reference_type' => 'GoodsReceived',
                    'reference_id' => $grn->id,
                    'note' => "GRN {$grn->grn_number} - From {$supplier->name}",
                ]);
            } else {
                $stockMain->increment('full_qty', $item['quantity']);
                $stockMain->decrement('empty_qty', $item['quantity']);

                StockMainLedger::create([
                    'cylinder_type_id' => $item['cylinder_type_id'],
                    'full_qty_change' => $item['quantity'],
                    'empty_qty_change' => -$item['quantity'],
                    'full_qty_after' => $stockMain->full_qty,
                    'empty_qty_after' => $stockMain->empty_qty,
                    'transaction_type' => 'grn_refill',
                    'reference_type' => 'GoodsReceived',
                    'reference_id' => $grn->id,
                    'note' => "GRN {$grn->grn_number} - Refill from {$supplier->name}",
                ]);
            }
        }

        $grn->update(['total_cost' => $totalCost]);

        return redirect()->route('warehouse.procurement')
            ->with('success', 'Procurement completed and stock updated.');
    }
}
