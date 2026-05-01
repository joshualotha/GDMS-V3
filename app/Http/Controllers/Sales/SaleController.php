<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\CylinderType;
use App\Models\OpeningStockItem;
use App\Models\Outlet;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    protected $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    public function index(Request $request)
    {
        $query = Sale::with('outlet');

        if ($request->has('outlet_id') && $request->outlet_id) {
            $query->where('outlet_id', $request->outlet_id);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }

        $sales = $query->orderBy('sale_date', 'desc')->get();
        $outlets = Outlet::where('is_active', true)->orderBy('name')->get();

        return view('sales.index', compact('sales', 'outlets'));
    }

    public function create()
    {
        $outlets = Outlet::where('is_active', true)->orderBy('name')->get();
        $cylinderTypes = CylinderType::where('is_active', true)->orderBy('size_kg')->get();

        $outletStock = [];
        foreach ($outlets as $outlet) {
            $outletStock[$outlet->id] = OpeningStockItem::whereHas('openingStock', fn ($q) => $q->where('outlet_id', $outlet->id))
                ->pluck('full_qty', 'cylinder_type_id')
                ->toArray();
        }

        return view('sales.create', compact('outlets', 'cylinderTypes', 'outletStock'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'sale_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.cylinder_type_id' => 'required|exists:cylinder_types,id',
            'items.*.sale_type' => 'required|in:full,refill',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $sale = $this->saleService->createSale(
                $validated['outlet_id'],
                $validated['sale_date'],
                $validated['items'],
                $validated['notes'] ?? null
            );

            return redirect()->route('sales.index')
                ->with('success', 'Sale recorded successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(Sale $sale)
    {
        $sale->load(['outlet', 'items.cylinderType']);

        return view('sales.show', compact('sale'));
    }

    public function getOutletStock(Outlet $outlet): JsonResponse
    {
        $stock = OpeningStockItem::whereHas('openingStock', fn ($q) => $q->where('outlet_id', $outlet->id))
            ->pluck('full_qty', 'cylinder_type_id')
            ->toArray();

        return response()->json($stock);
    }

    public function submitCash(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'cash_submitted' => 'required|numeric|min:0',
            'cash_receipt_image' => 'nullable|file|image',
        ]);

        $receiptPath = $sale->cash_receipt_image;
        if ($request->hasFile('cash_receipt_image')) {
            $receiptPath = $request->file('cash_receipt_image')->store('receipts', 'public');
        }

        $sale->update([
            'cash_submitted' => $validated['cash_submitted'],
            'cash_submitted_date' => now()->toDateString(),
            'cash_submitted_by' => auth()->id(),
            'cash_receipt_image' => $receiptPath,
        ]);

        return back()->with('success', 'Cash submitted successfully.');
    }
}
