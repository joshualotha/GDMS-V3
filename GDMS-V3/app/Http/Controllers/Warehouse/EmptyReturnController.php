<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\CylinderType;
use App\Models\EmptyReturn;
use App\Models\Outlet;
use App\Models\StockOutlet;
use App\Services\EmptyReturnService;
use Illuminate\Http\Request;

class EmptyReturnController extends Controller
{
    protected $returnService;

    public function __construct(EmptyReturnService $returnService)
    {
        $this->returnService = $returnService;
    }

    public function index()
    {
        $returns = EmptyReturn::with('outlet', 'items.cylinderType')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('warehouse.empty-returns.index', compact('returns'));
    }

    public function create()
    {
        $outlets = Outlet::where('is_active', true)->orderBy('name')->get();
        $cylinderTypes = CylinderType::where('is_active', true)->orderBy('size_kg')->get();

        return view('warehouse.empty-returns.create', compact('outlets', 'cylinderTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.cylinder_type_id' => 'required|exists:cylinder_types,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $return = $this->returnService->createReturn(
                $validated['outlet_id'],
                $validated['items'],
                $validated['notes'] ?? null
            );

            return redirect()->route('empty-returns.index')
                ->with('success', 'Empty return completed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(EmptyReturn $emptyReturn)
    {
        $emptyReturn->load(['outlet', 'items.cylinderType']);

        return view('warehouse.empty-returns.show', compact('emptyReturn'));
    }

    public function getStock($outletId)
    {
        $stock = StockOutlet::where('outlet_id', $outletId)->get()->keyBy('cylinder_type_id');

        return response()->json($stock->toArray());
    }
}
