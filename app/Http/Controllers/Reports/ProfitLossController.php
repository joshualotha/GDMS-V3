<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Expense;
use App\Models\PayrollPeriod;
use App\Models\PayrollItem;
use App\Models\DepreciationLog;
use App\Models\FuelPurchase;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProfitLossController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : Carbon::now()->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : Carbon::now()->endOfDay();

        // Sales Revenue (using sale_date, not created_at)
        $sales = Sale::whereBetween('sale_date', [$dateFrom->toDateString(), $dateTo->toDateString()])
            ->where('status', 'approved')
            ->get();
        $totalRevenue = $sales->sum('total_price');
        
        // COGS from SaleItems (more accurate)
        $salesIds = $sales->pluck('id');
        $totalCogs = SaleItem::whereIn('sale_id', $salesIds)->sum('total_cost');
        $grossProfit = $totalRevenue - $totalCogs;

        // Fuel Costs (using date field)
        $fuelCost = FuelPurchase::whereBetween('date', [$dateFrom->toDateString(), $dateTo->toDateString()])->sum('total_cost') ?? 0;

        // Payroll Costs (using approved periods)
        $payrollPeriods = PayrollPeriod::whereBetween('approved_at', [$dateFrom, $dateTo])
            ->whereIn('status', ['approved', 'paid'])
            ->get();
        $payrollCost = $payrollPeriods->sum('total_net') ?? 0;

        // Depreciation Costs
        $depreciationCost = DepreciationLog::whereBetween('created_at', [$dateFrom, $dateTo])->sum('depreciation_amount') ?? 0;

        // Other Expenses
        $expenses = Expense::whereBetween('expense_date', [$dateFrom->toDateString(), $dateTo->toDateString()])->get();
        $otherExpenses = $expenses->sum('amount') ?? 0;
        
        $totalOperatingExpenses = $fuelCost + $payrollCost + $depreciationCost + $otherExpenses;
        
        $netProfit = $grossProfit - $totalOperatingExpenses;

        return view('reports.profit-loss.index', compact(
            'dateFrom', 'dateTo',
            'totalRevenue',
            'totalCogs', 'grossProfit',
            'fuelCost', 'payrollCost', 'depreciationCost', 'otherExpenses', 'totalOperatingExpenses',
            'netProfit'
        ));
    }

    public function export(Request $request)
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : Carbon::now()->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : Carbon::now()->endOfDay();

        // Sales Revenue
        $sales = Sale::whereBetween('sale_date', [$dateFrom->toDateString(), $dateTo->toDateString()])
            ->where('status', 'approved')
            ->get();
        $totalRevenue = $sales->sum('total_price');
        $totalCogs = $sales->sum('total_cost');
        $grossProfit = $totalRevenue - $totalCogs;

        // Fuel Costs
        $fuelCost = FuelPurchase::whereBetween('date', [$dateFrom->toDateString(), $dateTo->toDateString()])->sum('total_cost') ?? 0;

        // Payroll Costs
        $payrollPeriods = PayrollPeriod::whereBetween('approved_at', [$dateFrom, $dateTo])
            ->whereIn('status', ['approved', 'paid'])
            ->get();
        $payrollCost = $payrollPeriods->sum('total_net') ?? 0;

        // Depreciation Costs
        $depreciationCost = DepreciationLog::whereBetween('created_at', [$dateFrom, $dateTo])->sum('depreciation_amount') ?? 0;

        // Other Expenses
        $expenses = Expense::whereBetween('expense_date', [$dateFrom->toDateString(), $dateTo->toDateString()])->get();
        $otherExpenses = $expenses->sum('amount') ?? 0;
        
        $totalOperatingExpenses = $fuelCost + $payrollCost + $depreciationCost + $otherExpenses;
        
        $netProfit = $grossProfit - $totalOperatingExpenses;

        $pdf = Pdf::loadView('reports.profit-loss.pdf', compact(
            'dateFrom', 'dateTo',
            'totalRevenue',
            'totalCogs', 'grossProfit',
            'fuelCost', 'payrollCost', 'depreciationCost', 'otherExpenses', 'totalOperatingExpenses',
            'netProfit'
        ));

        return $pdf->download('profit-loss-' . $dateFrom->format('Y-m-d') . '-to-' . $dateTo->format('Y-m-d') . '.pdf');
    }
}