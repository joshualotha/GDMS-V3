<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\FuelIssue;
use App\Models\FuelPurchase;
use App\Models\FuelStock;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FuelReportController extends Controller
{
    public function index(Request $request)
    {
        // Use 'date' field instead of created_at for filtering
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : '2024-01-01';
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : Carbon::now()->endOfDay();

        // Use actual date field for purchases
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
        $issues = $issuesQuery->with('asset')->orderBy('created_at', 'desc')->get();

        if ($request->fuel_type) {
            $purchasesQuery->where('fuel_type', $request->fuel_type);
            $issuesQuery->where('fuel_type', $request->fuel_type);
        }

        $purchases = $purchasesQuery->orderBy('created_at', 'desc')->get();
        $issues = $issuesQuery->with('asset')->orderBy('created_at', 'desc')->get();

        $fuelStock = FuelStock::all()->keyBy('fuel_type');

        $totals = [
            'diesel_purchased' => $purchases->where('fuel_type', 'diesel')->sum('litres'),
            'petrol_purchased' => $purchases->where('fuel_type', 'petrol')->sum('litres'),
            'diesel_issued' => $issues->where('fuel_type', 'diesel')->sum('litres'),
            'petrol_issued' => $issues->where('fuel_type', 'petrol')->sum('litres'),
        ];

        $balance = [
            'diesel' => ($fuelStock['diesel']->litres ?? 0),
            'petrol' => ($fuelStock['petrol']->litres ?? 0),
        ];

        return view('reports.fuel.index', compact(
            'purchases', 'issues', 'totals', 'balance', 'dateFrom', 'dateTo'
        ));
    }

    public function export(Request $request)
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : '2024-01-01';
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
        $issues = $issuesQuery->with('asset')->orderBy('created_at', 'desc')->get();

        if ($request->fuel_type) {
            $purchasesQuery->where('fuel_type', $request->fuel_type);
            $issuesQuery->where('fuel_type', $request->fuel_type);
        }

        $purchases = $purchasesQuery->orderBy('created_at', 'desc')->get();
        $issues = $issuesQuery->with('asset')->orderBy('created_at', 'desc')->get();

        $fuelStock = FuelStock::all()->keyBy('fuel_type');

        $totals = [
            'diesel_purchased' => $purchases->where('fuel_type', 'diesel')->sum('litres'),
            'petrol_purchased' => $purchases->where('fuel_type', 'petrol')->sum('litres'),
            'diesel_issued' => $issues->where('fuel_type', 'diesel')->sum('litres'),
            'petrol_issued' => $issues->where('fuel_type', 'petrol')->sum('litres'),
        ];

        $balance = [
            'diesel' => ($fuelStock['diesel']->litres ?? 0),
            'petrol' => ($fuelStock['petrol']->litres ?? 0),
        ];

        $pdf = Pdf::loadView('reports.fuel.pdf', compact(
            'purchases', 'issues', 'totals', 'balance', 'dateFrom', 'dateTo'
        ));

        return $pdf->download('fuel-report-'.$dateFrom->format('Y-m-d').'-to-'.$dateTo->format('Y-m-d').'.pdf');
    }
}
