<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CashReconciliationController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : Carbon::now()->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : Carbon::now()->endOfDay();

        $query = Sale::whereNotNull('cash_submitted');

        if ($request->outlet_id) {
            $query->where('outlet_id', $request->outlet_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $submissions = $query->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->with('outlet')
            ->orderBy('sale_date', 'desc')
            ->get();

        $outlets = Outlet::orderBy('name')->get();

        return view('reports.cash.index', compact(
            'submissions', 'outlets', 'dateFrom', 'dateTo'
        ));
    }

    public function export(Request $request)
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : Carbon::now()->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : Carbon::now()->endOfDay();

        $query = Sale::whereNotNull('cash_submitted');

        if ($request->outlet_id) {
            $query->where('outlet_id', $request->outlet_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $submissions = $query->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->with('outlet')
            ->orderBy('sale_date', 'desc')
            ->get();

        $outlets = Outlet::orderBy('name')->get();

        $pdf = Pdf::loadView('reports.cash.pdf', compact(
            'submissions', 'outlets', 'dateFrom', 'dateTo'
        ));

        return $pdf->download('cash-reconciliation-'.$dateFrom->format('Y-m-d').'-to-'.$dateTo->format('Y-m-d').'.pdf');
    }
}
