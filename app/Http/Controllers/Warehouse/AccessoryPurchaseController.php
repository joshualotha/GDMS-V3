<?php

namespace App\Http\Controllers\Warehouse;

use App\Helpers\ReferenceGenerator;
use App\Http\Controllers\Controller;
use App\Models\Accessory;
use App\Models\AccessoryPurchase;
use App\Models\StockMainAccessory;
use App\Models\Supplier;
use App\Services\AccessoryStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccessoryPurchaseController extends Controller
{
    protected $stockService;

    public function __construct(AccessoryStockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index()
    {
        $purchases = AccessoryPurchase::with('supplier', 'items.accessory')
            ->orderBy('purchase_date', 'desc')
            ->get();

        return view('warehouse.accessory-purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $accessories = Accessory::where('is_active', true)->orderBy('name')->get();
        $stockMain = StockMainAccessory::all()->keyBy('accessory_id');

        return view('warehouse.accessory-purchases.create', compact('suppliers', 'accessories', 'stockMain'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'receipt_number' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.accessory_id' => 'required|exists:accessories,id',
            'items.*.quantity' => 'required|integer|min:0',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        $items = array_filter($validated['items'], fn ($item) => $item['quantity'] > 0);

        if (empty($items)) {
            return back()->with('error', 'Please enter a quantity greater than 0 for at least one accessory.');
        }

        try {
            return DB::transaction(function () use ($validated, $items) {
                $purchase = AccessoryPurchase::create([
                    'purchase_number' => ReferenceGenerator::generateAccessoryPurchaseNumber(),
                    'purchase_date' => $validated['purchase_date'],
                    'supplier_id' => $validated['supplier_id'] ?? null,
                    'receipt_number' => $validated['receipt_number'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                    'status' => 'completed',
                ]);

                $totalCost = 0;

                foreach ($items as $item) {
                    $lineTotal = $item['quantity'] * $item['unit_cost'];
                    $purchase->items()->create([
                        'accessory_id' => $item['accessory_id'],
                        'quantity' => $item['quantity'],
                        'unit_cost' => $item['unit_cost'],
                        'line_total' => $lineTotal,
                    ]);
                    $totalCost += $lineTotal;

                    $this->stockService->updateMainStock(
                        $item['accessory_id'],
                        $item['quantity'],
                        'accessory_purchase',
                        'AccessoryPurchase',
                        $purchase->id,
                        "Accessory purchase {$purchase->purchase_number}",
                        $purchase->purchase_date->toDateString()
                    );
                }

                $purchase->update(['total_cost' => $totalCost]);

                return redirect()->route('accessory-purchases.index')
                    ->with('success', 'Accessory purchase recorded and stock updated.');
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(Request $request, AccessoryPurchase $accessoryPurchase)
    {
        if ($accessoryPurchase->status === 'cancelled') {
            return back()->with('error', 'This purchase is already cancelled.');
        }

        $validated = $request->validate(['reason' => 'required|string']);

        try {
            DB::transaction(function () use ($accessoryPurchase, $validated) {
                $accessoryPurchase->load('items');

                foreach ($accessoryPurchase->items as $item) {
                    $this->stockService->updateMainStock(
                        $item->accessory_id,
                        -$item->quantity,
                        'accessory_purchase_cancel',
                        'AccessoryPurchase',
                        $accessoryPurchase->id,
                        "Cancellation of purchase {$accessoryPurchase->purchase_number}",
                        $accessoryPurchase->purchase_date->toDateString()
                    );
                }

                $accessoryPurchase->update([
                    'status' => 'cancelled',
                    'notes' => trim(($accessoryPurchase->notes ?? '')."\n[Cancelled]: ".$validated['reason']),
                ]);
            });

            return redirect()->route('accessory-purchases.index')
                ->with('success', 'Purchase cancelled and stock reversed.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
