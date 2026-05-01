<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\CylinderType;
use App\Models\Outlet;
use App\Models\Sale;
use App\Models\SaleItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : Carbon::now()->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : Carbon::now()->endOfDay();

        $query = Sale::whereBetween('sale_date', [$dateFrom, $dateTo]);

        if ($request->outlet_id) {
            $query->where('outlet_id', $request->outlet_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $sales = $query->orderBy('sale_date', 'desc')->get();
        
        $saleItems = SaleItem::whereHas('sale', function ($q) use ($dateFrom, $dateTo, $request) {
            $q->whereBetween('sale_date', [$dateFrom, $dateTo]);
            if ($request->outlet_id) {
                $q->where('outlet_id', $request->outlet_id);
            }
            if ($request->status) {
                $q->where('status', $request->status);
            }
        })->with(['sale', 'cylinderType'])->get();

        if ($request->cylinder_type_id) {
            $saleItems = $saleItems->where('cylinder_type_id', $request->cylinder_type_id);
        }

        $outlets = Outlet::orderBy('name')->get();
        $cylinderTypes = CylinderType::where('is_active', true)->orderBy('size_kg')->get();

        $totals = [
            'total_qty' => $saleItems->sum('quantity'),
            'total_revenue' => $saleItems->sum('total_price'),
            'total_cogs' => $saleItems->sum('total_cost'),
        ];
        $totals['total_profit'] = $totals['total_revenue'] - $totals['total_cogs'];

        return view('reports.sales.index', compact(
            'sales', 'saleItems', 'outlets', 'cylinderTypes', 'totals', 'dateFrom', 'dateTo'
        ));
    }

    public function export(Request $request)
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : Carbon::now()->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : Carbon::now()->endOfDay();

        $query = Sale::whereBetween('sale_date', [$dateFrom, $dateTo]);

        if ($request->outlet_id) {
            $query->where('outlet_id', $request->outlet_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $sales = $query->orderBy('sale_date', 'desc')->get();
        
        $saleItems = SaleItem::whereHas('sale', function ($q) use ($dateFrom, $dateTo, $request) {
            $q->whereBetween('sale_date', [$dateFrom, $dateTo]);
            if ($request->outlet_id) {
                $q->where('outlet_id', $request->outlet_id);
            }
            if ($request->status) {
                $q->where('status', $request->status);
            }
        })->with(['sale', 'cylinderType'])->get();

        if ($request->cylinder_type_id) {
            $saleItems = $saleItems->where('cylinder_type_id', $request->cylinder_type_id);
        }

        $outlets = Outlet::orderBy('name')->get();
        $cylinderTypes = CylinderType::where('is_active', true)->orderBy('size_kg')->get();

        $totals = [
            'total_qty' => $saleItems->sum('quantity'),
            'total_revenue' => $saleItems->sum('total_price'),
            'total_cogs' => $saleItems->sum('total_cost'),
        ];
        $totals['total_profit'] = $totals['total_revenue'] - $totals['total_cogs'];

        $pdf = Pdf::loadView('reports.sales.pdf', compact(
            'saleItems', 'outlets', 'cylinderTypes', 'totals', 'dateFrom', 'dateTo'
        ));

        return $pdf->download('sales-report-' . $dateFrom->format('Y-m-d') . '-to-' . $dateTo->format('Y-m-d') . '.pdf');
    }
}