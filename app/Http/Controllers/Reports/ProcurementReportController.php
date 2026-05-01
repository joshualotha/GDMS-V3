<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\CylinderType;
use App\Models\GoodsReceived;
use App\Models\GoodsReceivedItem;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProcurementReportController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : Carbon::now()->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : Carbon::now()->endOfDay();

        $query = GoodsReceived::whereBetween('created_at', [$dateFrom, $dateTo]);

        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $goodsReceived = $query->orderBy('created_at', 'desc')->get();

        $items = GoodsReceivedItem::whereHas('goodsReceived', function ($q) use ($dateFrom, $dateTo, $request) {
            $q->whereBetween('created_at', [$dateFrom, $dateTo]);
            if ($request->supplier_id) {
                $q->where('supplier_id', $request->supplier_id);
            }
        })->with(['goodsReceived', 'cylinderType'])->get();

        if ($request->cylinder_type_id) {
            $items = $items->where('cylinder_type_id', $request->cylinder_type_id);
        }

        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $cylinderTypes = CylinderType::where('is_active', true)->orderBy('size_kg')->get();

        $totalQty = $items->sum('quantity');
        $totalCost = $items->sum('line_total');

        return view('reports.procurement.index', compact(
            'items', 'suppliers', 'cylinderTypes', 'totalQty', 'totalCost', 'dateFrom', 'dateTo'
        ));
    }

    public function export(Request $request)
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : Carbon::now()->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : Carbon::now()->endOfDay();

        $query = GoodsReceived::whereBetween('created_at', [$dateFrom, $dateTo]);

        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $goodsReceived = $query->orderBy('created_at', 'desc')->get();

        $items = GoodsReceivedItem::whereHas('goodsReceived', function ($q) use ($dateFrom, $dateTo, $request) {
            $q->whereBetween('created_at', [$dateFrom, $dateTo]);
            if ($request->supplier_id) {
                $q->where('supplier_id', $request->supplier_id);
            }
        })->with(['goodsReceived', 'cylinderType'])->get();

        if ($request->cylinder_type_id) {
            $items = $items->where('cylinder_type_id', $request->cylinder_type_id);
        }

        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $cylinderTypes = CylinderType::where('is_active', true)->orderBy('size_kg')->get();

        $totalQty = $items->sum('quantity');
        $totalCost = $items->sum('line_total');

        $pdf = Pdf::loadView('reports.procurement.pdf', compact(
            'items', 'suppliers', 'cylinderTypes', 'totalQty', 'totalCost', 'dateFrom', 'dateTo'
        ));

        return $pdf->download('procurement-report-' . $dateFrom->format('Y-m-d') . '-to-' . $dateTo->format('Y-m-d') . '.pdf');
    }
}