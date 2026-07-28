<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\CylinderType;
use App\Models\GoodsReceived;
use App\Models\StockMain;
use App\Models\Supplier;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcurementController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

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
            'items.*.quantity' => 'required|integer|min:0',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        $items = array_filter($validated['items'], fn ($item) => $item['quantity'] > 0);

        if (empty($items)) {
            return back()->with('error', 'Please enter a quantity greater than 0 for at least one cylinder type.');
        }

        $validated['items'] = $items;

        try {
            return DB::transaction(function () use ($validated) {
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

                    if ($item['purchase_type'] == 'full') {
                        $this->stockService->updateMainStock(
                            $item['cylinder_type_id'],
                            $item['quantity'],
                            0,
                            'grn_full',
                            'GoodsReceived',
                            $grn->id,
                            "GRN {$grn->grn_number} - From {$supplier->name}"
                        );
                    } else {
                        $this->stockService->updateMainStock(
                            $item['cylinder_type_id'],
                            $item['quantity'],
                            -$item['quantity'],
                            'grn_refill',
                            'GoodsReceived',
                            $grn->id,
                            "GRN {$grn->grn_number} - Refill from {$supplier->name}"
                        );
                    }
                }

                $grn->update(['total_cost' => $totalCost]);

                return redirect()->route('warehouse.procurement')
                    ->with('success', 'Procurement completed and stock updated.');
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
