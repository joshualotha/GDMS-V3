<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\CylinderType;
use App\Models\EmptyReturn;
use App\Models\Outlet;
use App\Models\StockMain;
use App\Models\StockOutlet;
use App\Models\StockTransfer;
use App\Services\EmptyReturnService;
use App\Services\TransferService;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    protected $transferService;
    protected $emptyReturnService;

    public function __construct(TransferService $transferService, EmptyReturnService $emptyReturnService)
    {
        $this->transferService = $transferService;
        $this->emptyReturnService = $emptyReturnService;
    }

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
                $this->emptyReturnService->createReturn($validated['outlet_id'], $items, $validated['notes'] ?? null);

                return redirect()->route('warehouse.movements', ['type' => 'return'])
                    ->with('success', 'Empty return recorded successfully.');
            } else {
                $this->transferService->createTransfer($validated['outlet_id'], $items, $validated['notes'] ?? null);

                return redirect()->route('warehouse.movements', ['type' => 'transfer'])
                    ->with('success', 'Transfer completed successfully.');
            }
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function getStock($outletId)
    {
        $stock = StockOutlet::where('outlet_id', $outletId)->get()->keyBy('cylinder_type_id');

        return response()->json($stock->toArray());
    }
}
