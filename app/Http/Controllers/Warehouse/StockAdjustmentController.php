<?php

namespace App\Http\Controllers\Warehouse;

use App\Helpers\ReferenceGenerator;
use App\Http\Controllers\Controller;
use App\Models\CylinderType;
use App\Models\Outlet;
use App\Models\StockAdjustment;
use App\Models\StockMain;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index()
    {
        $adjustments = StockAdjustment::with('cylinderType')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('warehouse.adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        $outlets = Outlet::where('is_active', true)->orderBy('name')->get();
        $cylinderTypes = CylinderType::where('is_active', true)->orderBy('size_kg')->get();
        $stockMain = StockMain::with('cylinderType')->get()->mapWithKeys(function ($item) {
            return [$item->cylinder_type_id => $item];
        });

        return view('warehouse.adjustments.create', compact('outlets', 'cylinderTypes', 'stockMain'));
    }

    public function store(Request $request)
    {
        $isMain = $request->input('is_main') == '1';

        $rules = [
            'is_main' => 'required|in:0,1',
            'cylinder_type_id' => 'required|exists:cylinder_types,id',
            'type' => 'required|in:gain,loss,correction',
            'full_qty_change' => 'required|integer',
            'empty_qty_change' => 'required|integer',
            'reason' => 'required|string',
        ];

        if (! $isMain) {
            $rules['outlet_id'] = 'required|exists:outlets,id';
        }

        $validated = $request->validate($rules);

        $outletId = $isMain ? null : ($validated['outlet_id'] ?? null);

        return DB::transaction(function () use ($validated, $isMain, $outletId) {
            $adjustment = StockAdjustment::create([
                'adjustment_number' => ReferenceGenerator::generateAdjustmentNumber(),
                'outlet_id' => $outletId,
                'is_main' => $isMain,
                'cylinder_type_id' => $validated['cylinder_type_id'],
                'type' => $validated['type'],
                'full_qty_change' => $validated['full_qty_change'],
                'empty_qty_change' => $validated['empty_qty_change'],
                'reason' => $validated['reason'],
            ]);

            $fullChange = $validated['type'] == 'loss' ? -$validated['full_qty_change'] : $validated['full_qty_change'];
            $emptyChange = $validated['type'] == 'loss' ? -$validated['empty_qty_change'] : $validated['empty_qty_change'];

            if ($isMain) {
                $this->stockService->updateMainStock(
                    $validated['cylinder_type_id'],
                    $fullChange,
                    $emptyChange,
                    'adjustment',
                    'StockAdjustment',
                    $adjustment->id,
                    "{$validated['type']}: {$validated['reason']}"
                );
            } else {
                $this->stockService->updateOutletStock(
                    $outletId,
                    $validated['cylinder_type_id'],
                    $fullChange,
                    $emptyChange,
                    'adjustment',
                    'StockAdjustment',
                    $adjustment->id,
                    "{$validated['type']}: {$validated['reason']}"
                );
            }

            return redirect()->route('stock-adjustments.index')
                ->with('success', 'Stock adjustment posted successfully.');
        });
    }
}
