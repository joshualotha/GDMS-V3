<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGoodsReceivedRequest;
use App\Helpers\ReferenceGenerator;
use App\Models\CylinderType;
use App\Models\GoodsReceived;
use App\Models\GoodsReceivedItem;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoodsReceivedController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index()
    {
        $goodsReceived = GoodsReceived::with('supplier')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('procurement.goods-received.index', compact('goodsReceived'));
    }

    public function create(Request $request)
    {
        $grnNumber = ReferenceGenerator::generateGrnNumber();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $cylinderTypes = CylinderType::where('is_active', true)->orderBy('size_kg')->get();

        $selectedPo = null;
        if ($request->has('po_id')) {
            $selectedPo = PurchaseOrder::with('items.cylinderType')
                ->where('id', $request->po_id)
                ->where('status', 'pending')
                ->first();
        }

        return view('procurement.goods-received.create', compact('grnNumber', 'suppliers', 'cylinderTypes', 'selectedPo'));
    }

    public function store(StoreGoodsReceivedRequest $request)
    {
        $validated = $request->validated();

        try {
            return DB::transaction(function () use ($validated) {
                $purchaseOrder = null;

                if (!empty($validated['purchase_order_id'])) {
                    $purchaseOrder = PurchaseOrder::with('items')
                        ->lockForUpdate()
                        ->findOrFail($validated['purchase_order_id']);

                    if ($purchaseOrder->status !== 'pending') {
                        throw new \Exception("This purchase order has already been received (status: {$purchaseOrder->status}).");
                    }

                    foreach ($validated['items'] as $item) {
                        $poItem = $purchaseOrder->items->first(fn ($poi) =>
                            $poi->cylinder_type_id == $item['cylinder_type_id']
                            && $poi->purchase_type == $item['purchase_type']
                        );
                        $orderedQty = $poItem ? $poItem->quantity : 0;

                        if ($item['quantity'] > $orderedQty) {
                            $cylinderType = CylinderType::find($item['cylinder_type_id']);
                            throw new \Exception("Cannot receive {$item['quantity']} {$item['purchase_type']} {$cylinderType->name} — only {$orderedQty} was ordered on this purchase order.");
                        }
                    }
                }

                $grn = GoodsReceived::create([
                    'grn_number' => ReferenceGenerator::generateGrnNumber(),
                    'supplier_id' => $validated['supplier_id'],
                    'purchase_order_id' => $validated['purchase_order_id'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                ]);

                $totalCost = 0;

                foreach ($validated['items'] as $item) {
                    $lineTotal = $item['quantity'] * $item['unit_cost'];
                    GoodsReceivedItem::create([
                        'goods_received_id' => $grn->id,
                        'cylinder_type_id' => $item['cylinder_type_id'],
                        'purchase_type' => $item['purchase_type'],
                        'quantity' => $item['quantity'],
                        'unit_cost' => $item['unit_cost'],
                        'line_total' => $lineTotal,
                    ]);
                    $totalCost += $lineTotal;

                    // Update stock based on purchase type
                    if ($item['purchase_type'] == 'full') {
                        // Full cylinders purchased from factory: +full stock
                        $this->stockService->updateMainStock(
                            $item['cylinder_type_id'],
                            $item['quantity'],  // +full
                            0,                 // no empty change
                            'grn_full',
                            'GoodsReceived',
                            $grn->id,
                            "GRN: {$grn->grn_number} - Full cylinders received"
                        );
                    } else {
                        // Refill: we exchange empty cylinders for full ones from factory
                        // We receive full cylinders, so: +full, -empty (empties go to factory)
                        $this->stockService->updateMainStock(
                            $item['cylinder_type_id'],
                            $item['quantity'],   // +full (fulls received from factory)
                            -$item['quantity'], // -empty (empties sent to factory for refill)
                            'grn_refill',
                            'GoodsReceived',
                            $grn->id,
                            "GRN: {$grn->grn_number} - Refill exchange"
                        );
                    }
                }

                $grn->update(['total_cost' => $totalCost, 'status' => 'completed']);

                if ($purchaseOrder) {
                    $purchaseOrder->update(['status' => 'received']);
                }

                return redirect()->route('goods-received.index')
                    ->with('success', 'Goods received and stock updated successfully.');
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(GoodsReceived $goodsReceived)
    {
        $goodsReceived->load(['supplier', 'items.cylinderType', 'purchaseOrder']);
        return view('procurement.goods-received.show', compact('goodsReceived'));
    }
}