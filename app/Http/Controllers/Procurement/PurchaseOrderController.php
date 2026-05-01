<?php

namespace App\Http\Controllers\Procurement;

use App\Helpers\ReferenceGenerator;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePurchaseOrderRequest;
use App\Models\CylinderType;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with('supplier')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('procurement.purchase-orders.index', compact('purchaseOrders'));
    }

    public function create()
    {
        $poNumber = ReferenceGenerator::generatePoNumber();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $cylinderTypes = CylinderType::where('is_active', true)->orderBy('size_kg')->get();

        return view('procurement.purchase-orders.create', compact('poNumber', 'suppliers', 'cylinderTypes'));
    }

    public function store(StorePurchaseOrderRequest $request)
    {
        $validated = $request->validated();

        $po = PurchaseOrder::create([
            'po_number' => ReferenceGenerator::generatePoNumber(),
            'supplier_id' => $validated['supplier_id'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $totalCost = 0;
        foreach ($validated['items'] as $item) {
            $lineTotal = $item['quantity'] * $item['unit_cost'];
            PurchaseOrderItem::create([
                'purchase_order_id' => $po->id,
                'cylinder_type_id' => $item['cylinder_type_id'],
                'purchase_type' => $item['purchase_type'],
                'quantity' => $item['quantity'],
                'unit_cost' => $item['unit_cost'],
                'line_total' => $lineTotal,
            ]);
            $totalCost += $lineTotal;
        }

        $po->update(['total_cost' => $totalCost]);

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase order created successfully.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.cylinderType']);

        return view('procurement.purchase-orders.show', compact('purchaseOrder'));
    }

    public function items(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['items.cylinderType']);

        return response()->json([
            'items' => $purchaseOrder->items->toArray(),
        ]);
    }
}
