<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Accessory;
use App\Models\AccessoryTransfer;
use App\Models\Outlet;
use App\Models\StockMainAccessory;
use App\Models\StockOutletAccessory;
use App\Services\AccessoryTransferService;
use Illuminate\Http\Request;

class AccessoryTransferController extends Controller
{
    protected $transferService;

    public function __construct(AccessoryTransferService $transferService)
    {
        $this->transferService = $transferService;
    }

    public function index()
    {
        $transfers = AccessoryTransfer::with('outlet', 'items.accessory')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('warehouse.accessory-transfers.index', compact('transfers'));
    }

    public function create()
    {
        $outlets = Outlet::where('is_active', true)->orderBy('name')->get();
        $accessories = Accessory::where('is_active', true)->orderBy('name')->get();

        $stockMain = StockMainAccessory::all()->keyBy('accessory_id');

        return view('warehouse.accessory-transfers.create', compact('outlets', 'accessories', 'stockMain'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'transfer_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.accessory_id' => 'required|exists:accessories,id',
            'items.*.quantity' => 'required|integer|min:0',
        ]);

        $items = array_filter($validated['items'], fn ($item) => isset($item['quantity']) && $item['quantity'] > 0);

        if (empty($items)) {
            return back()->with('error', 'Please enter at least one quantity greater than 0.');
        }

        try {
            $this->transferService->createTransfer($validated['outlet_id'], $items, $validated['notes'] ?? null, $validated['transfer_date']);

            return redirect()->route('accessory-transfers.index')
                ->with('success', 'Accessory transfer completed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(Request $request, AccessoryTransfer $accessoryTransfer)
    {
        $validated = $request->validate(['reason' => 'required|string']);

        try {
            $this->transferService->cancelTransfer($accessoryTransfer, $validated['reason']);

            return redirect()->route('accessory-transfers.index')
                ->with('success', 'Transfer cancelled and stock reversed.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function getStock($outletId)
    {
        $stock = StockOutletAccessory::where('outlet_id', $outletId)->get()->keyBy('accessory_id');

        return response()->json($stock->toArray());
    }
}
