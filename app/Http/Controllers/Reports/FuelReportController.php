<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\FuelIssue;
use App\Models\FuelPurchase;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FuelReportController extends Controller
{
    public function index(Request $request)
    {
        [$purchases, $issues, $totals, $dateFrom, $dateTo] = $this->buildReportData($request);

        return view('reports.fuel.index', compact(
            'purchases', 'issues', 'totals', 'dateFrom', 'dateTo'
        ));
    }

    public function export(Request $request)
    {
        [$purchases, $issues, $totals, $dateFrom, $dateTo] = $this->buildReportData($request);

        $pdf = Pdf::loadView('reports.fuel.pdf', compact(
            'purchases', 'issues', 'totals', 'dateFrom', 'dateTo'
        ));

        return $pdf->download('fuel-report-'.$dateFrom->format('Y-m-d').'-to-'.$dateTo->format('Y-m-d').'.pdf');
    }

    protected function buildReportData(Request $request): array
    {
        // Use 'date' field instead of created_at for filtering
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : Carbon::parse('2024-01-01');
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : Carbon::now()->endOfDay();

        $purchasesQuery = FuelPurchase::whereNotNull('id');
        $issuesQuery = FuelIssue::whereBetween('created_at', [$dateFrom, $dateTo]);

        if ($request->date_from) {
            $purchasesQuery->whereDate('date', '>=', $dateFrom);
        }
        if ($request->date_to) {
            $purchasesQuery->whereDate('date', '<=', $dateTo);
        }

        if ($request->fuel_type) {
            $purchasesQuery->where('fuel_type', $request->fuel_type);
            $issuesQuery->where('fuel_type', $request->fuel_type);
        }

        $purchases = $purchasesQuery->orderBy('date', 'desc')->get();
        $issues = $issuesQuery->with('outlet')->orderBy('created_at', 'desc')->get();

        $totals = [
            'diesel_purchased' => $purchases->where('fuel_type', 'diesel')->sum('litres'),
            'petrol_purchased' => $purchases->where('fuel_type', 'petrol')->sum('litres'),
            'diesel_issued' => $issues->where('fuel_type', 'diesel')->sum('litres'),
            'petrol_issued' => $issues->where('fuel_type', 'petrol')->sum('litres'),
        ];

        return [$purchases, $issues, $totals, $dateFrom, $dateTo];
    }
}
