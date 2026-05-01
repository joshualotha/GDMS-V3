<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\CylinderType;
use App\Models\OpeningStock;
use App\Models\OpeningStockItem;
use App\Models\StockMain;
use App\Models\StockOutlet;
use App\Models\Outlet;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpeningStockController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index()
    {
        $openingStocks = OpeningStock::with('outlet', 'items.cylinderType')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('warehouse.opening-stock.index', compact('openingStocks'));
    }

    public function create()
    {
        $cylinderTypes = CylinderType::where('is_active', true)->orderBy('size_kg')->get();
        $outlets = Outlet::where('is_active', true)->orderBy('name')->get();
        return view('warehouse.opening-stock.create', compact('cylinderTypes', 'outlets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'outlet_id' => 'nullable|exists:outlets,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.cylinder_type_id' => 'required|exists:cylinder_types,id',
            'items.*.full_qty' => 'required|integer|min:0',
            'items.*.empty_qty' => 'required|integer|min:0',
        ]);

        $outletId = $validated['outlet_id'] ?? null;

        $exists = OpeningStock::where('outlet_id', $outletId)->exists();
        if ($exists) {
            $location = $outletId ? Outlet::find($outletId)->name : 'Main Store';
            return redirect()->route('opening-stock.index')->with('error', "Opening stock already exists for {$location}. Use Adjustments to modify stock.");
        }

        return DB::transaction(function () use ($validated, $outletId) {
            $opening = OpeningStock::create([
                'reference' => 'OP-' . date('Ymd') . '-' . str_pad(OpeningStock::count() + 1, 4, '0', STR_PAD_LEFT),
                'outlet_id' => $outletId,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                if ($item['full_qty'] > 0 || $item['empty_qty'] > 0) {
                    OpeningStockItem::create([
                        'opening_stock_id' => $opening->id,
                        'cylinder_type_id' => $item['cylinder_type_id'],
                        'full_qty' => $item['full_qty'],
                        'empty_qty' => $item['empty_qty'],
                    ]);

                    if ($outletId) {
                        $stockOutlet = StockOutlet::firstOrCreate(
                            ['outlet_id' => $outletId, 'cylinder_type_id' => $item['cylinder_type_id']],
                            ['full_qty' => 0, 'empty_qty' => 0]
                        );
                        $stockOutlet->update([
                            'full_qty' => $stockOutlet->full_qty + $item['full_qty'],
                            'empty_qty' => $stockOutlet->empty_qty + $item['empty_qty'],
                        ]);
                    } else {
                        $this->stockService->updateMainStock(
                            $item['cylinder_type_id'],
                            $item['full_qty'],
                            $item['empty_qty'],
                            'opening',
                            'OpeningStock',
                            $opening->id,
                            'Opening stock entry'
                        );
                    }
                }
            }

            $locationName = $outletId ? Outlet::find($outletId)->name : 'Main Store';
            return redirect()->route('opening-stock.index')
                ->with('success', "Opening stock posted to {$locationName}.");
        });
    }
}