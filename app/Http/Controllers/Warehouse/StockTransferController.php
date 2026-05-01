<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\CylinderType;
use App\Models\Outlet;
use App\Models\StockMain;
use App\Models\StockTransfer;
use App\Services\TransferService;
use Illuminate\Http\Request;

class StockTransferController extends Controller
{
    protected $transferService;

    public function __construct(TransferService $transferService)
    {
        $this->transferService = $transferService;
    }

    public function index()
    {
        $transfers = StockTransfer::with('outlet', 'items.cylinderType')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('warehouse.transfers.index', compact('transfers'));
    }

    public function create()
    {
        $outlets = Outlet::where('is_active', true)->orderBy('name')->get();
        $cylinderTypes = CylinderType::where('is_active', true)->orderBy('size_kg')->get();

        $stockMain = StockMain::with('cylinderType')->get()->mapWithKeys(function ($item) {
            return [$item->cylinder_type_id => $item];
        });

        return view('warehouse.transfers.create', compact('outlets', 'cylinderTypes', 'stockMain'));
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
            $transfer = $this->transferService->createTransfer(
                $validated['outlet_id'],
                $validated['items'],
                $validated['notes'] ?? null
            );

            return redirect()->route('stock-transfers.index')
                ->with('success', 'Transfer completed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(StockTransfer $stockTransfer)
    {
        $stockTransfer->load(['outlet', 'items.cylinderType']);

        return view('warehouse.transfers.show', compact('stockTransfer'));
    }
}
