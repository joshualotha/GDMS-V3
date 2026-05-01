<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CylinderType;
use App\Models\Outlet;
use App\Models\StockMain;
use App\Models\StockOutlet;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\CashSubmission;
use App\Models\CompanyAsset;
use App\Models\StockTransfer;
use App\Models\GoodsReceived;
use App\Models\MaintenanceLog;
use App\Models\FuelStock;
use App\Models\PurchaseOrder;
use App\Models\Expense;
use App\Models\PayrollPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'this_month');
        
        $dateRange = $this->getDateRange($period);
        
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];
        $periodLabel = $dateRange['label'];

        $cylinderTypes = CylinderType::where('is_active', true)->orderBy('name')->get();
        $outlets = Outlet::where('is_active', true)->orderBy('name')->get();

        $mainStoreStock = StockMain::with('cylinderType')
            ->whereHas('cylinderType', fn($q) => $q->where('is_active', true))
            ->get();

        $totalFullCylinders = $mainStoreStock->sum('full_qty');
        $totalEmptyCylinders = $mainStoreStock->sum('empty_qty');

        $pendingApprovals = Sale::where('status', 'pending')->count() 
            + CashSubmission::where('status', 'pending')->count();

        $cashPendingReconciliation = CashSubmission::where('status', 'pending')
            ->sum('submitted_amount');

        $activeAssets = CompanyAsset::where('status', 'active')->count();

        $outletStockSummary = [];
        foreach ($outlets as $outlet) {
            $stock = StockOutlet::where('outlet_id', $outlet->id)
                ->whereHas('cylinderType', fn($q) => $q->where('is_active', true))
                ->get();
            
            $lastSale = Sale::where('outlet_id', $outlet->id)
                ->where('status', 'approved')
                ->orderBy('created_at', 'desc')
                ->first();

            $pendingSubmission = CashSubmission::whereHas('sale', fn($q) => $q->where('outlet_id', $outlet->id))
                ->where('status', 'pending')
                ->exists();

            $outletStockSummary[] = [
                'outlet' => $outlet,
                'full_qty' => $stock->sum('full_qty'),
                'empty_qty' => $stock->sum('empty_qty'),
                'last_sale_date' => $lastSale?->created_at,
                'pending_submission' => $pendingSubmission,
            ];
        }

        $stockByType = [];
        foreach ($cylinderTypes as $type) {
            $mainFull = StockMain::where('cylinder_type_id', $type->id)->first()?->full_qty ?? 0;
            $mainEmpty = StockMain::where('cylinder_type_id', $type->id)->first()?->empty_qty ?? 0;
            $outletFull = StockOutlet::where('cylinder_type_id', $type->id)->sum('full_qty');
            $outletEmpty = StockOutlet::where('cylinder_type_id', $type->id)->sum('empty_qty');

            $stockByType[] = [
                'type' => $type->name,
                'main_full' => $mainFull,
                'main_empty' => $mainEmpty,
                'outlet_full' => $outletFull,
                'outlet_empty' => $outletEmpty,
                'total_full' => $mainFull + $outletFull,
                'total_empty' => $mainEmpty + $outletEmpty,
                'reorder_level' => $type->reorder_level ?? 10,
            ];
        }

        $alerts = [];

        $lowStockAlerts = collect();
        foreach ($outlets as $outlet) {
            $hasStock = StockOutlet::where('outlet_id', $outlet->id)
                ->where('full_qty', '>', 0)
                ->exists();
            if (!$hasStock) {
                $alerts[] = [
                    'type' => 'danger',
                    'message' => "Outlet '{$outlet->name}' has zero full cylinders"
                ];
            }
        }

        foreach ($stockByType as $stock) {
            if ($stock['total_full'] > 0 && $stock['total_full'] <= $stock['reorder_level']) {
                $alerts[] = [
                    'type' => 'warning',
                    'message' => "Low stock: {$stock['type']} has only {$stock['total_full']} cylinders (reorder: {$stock['reorder_level']})"
                ];
            }
        }

        $sevenDaysFromNow = now()->addDays(7);
        $maintenanceDue = MaintenanceLog::where('next_service_date', '<=', $sevenDaysFromNow)
            ->whereHas('asset', fn($q) => $q->where('status', 'active'))
            ->with('asset')
            ->get();
        
        foreach ($maintenanceDue as $log) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "Asset '{$log->asset->name}' service due on " . $log->next_service_date->format('d M Y')
            ];
        }

        $varianceSubmissions = CashSubmission::where('status', 'reconciled')
            ->where('variance', '!=', 0)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($varianceSubmissions as $sub) {
            $alerts[] = [
                'type' => 'orange',
                'message' => "Cash submission CS-{$sub->submission_number} has variance of " . number_format($sub->variance, 2)
            ];
        }

        $recentSales = Sale::with('outlet')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentTransfers = StockTransfer::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentGRNs = GoodsReceived::with('purchaseOrder')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentActivity = collect()
            ->concat($recentSales->map(fn($s) => [
                'type' => 'Sale',
                'ref' => 'SL-' . $s->sale_number,
                'date' => $s->created_at,
                'outlet' => $s->outlet?->name,
            ]))
            ->concat($recentTransfers->map(fn($t) => [
                'type' => 'Transfer',
                'ref' => 'ST-' . $t->transfer_number,
                'date' => $t->created_at,
                'outlet' => $t->toOutlet?->name,
            ]))
            ->concat($recentGRNs->map(fn($g) => [
                'type' => 'GRN',
                'ref' => 'GRN-' . $g->grn_number,
                'date' => $g->created_at,
                'outlet' => null,
            ]))
            ->sortByDesc('date')
            ->take(10)
            ->values();

        // NEW: Sales Metrics
        $todayStart = now()->startOfDay();
        $weekStart = now()->startOfWeek();
        $monthStart = now()->startOfMonth();

        $salesTodayCount = Sale::where('status', 'approved')
            ->where('sale_date', '>=', $todayStart->toDateString())
            ->count();
        
        $salesTodayAmount = Sale::where('status', 'approved')
            ->where('sale_date', '>=', $todayStart->toDateString())
            ->sum('total_price');

        $salesWeekAmount = Sale::where('status', 'approved')
            ->where('sale_date', '>=', $weekStart->toDateString())
            ->sum('total_price');

        $salesMonthAmount = Sale::where('status', 'approved')
            ->where('sale_date', '>=', $monthStart->toDateString())
            ->sum('total_price');

        // Period-based sales metrics (using sale_date)
        $periodSalesCount = Sale::where('status', 'approved')
            ->whereBetween('sale_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->count();
        
        $periodSalesAmount = Sale::where('status', 'approved')
            ->whereBetween('sale_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->sum('total_price');

        // Period-based expenses
        $periodExpenses = Expense::whereBetween('created_at', [$startDate, $endDate])->sum('amount');

        // NEW: Fuel Stock
        $fuelStocks = FuelStock::all()->mapWithKeys(function($stock) {
            return [ucfirst($stock->fuel_type) => $stock->litres];
        });

        // NEW: Pending Tasks
        $pendingPOs = PurchaseOrder::where('status', 'pending')->count();
        $pendingGRNs = GoodsReceived::where('status', 'pending')->count();

        // NEW: Expenses this month
        $expensesMonth = Expense::where('created_at', '>=', $monthStart)->sum('amount');

        // Profit calculation
        $periodProfit = $periodSalesAmount - $periodExpenses;
        $profitMargin = $periodSalesAmount > 0 ? round(($periodProfit / $periodSalesAmount) * 100, 1) : 0;

        // This month's profit (always current month)
        $thisMonthStart = now()->startOfMonth();
        $thisMonthSales = Sale::where('status', 'approved')
            ->where('sale_date', '>=', $thisMonthStart->toDateString())
            ->sum('total_price');
        $thisMonthExpenses = Expense::where('created_at', '>=', $thisMonthStart)->sum('amount');
        $monthProfit = $thisMonthSales - $thisMonthExpenses;
        $monthProfitMargin = $thisMonthSales > 0 ? round(($monthProfit / $thisMonthSales) * 100, 1) : 0;

        // NEW: Payroll Summary
        $lastPayrollPeriod = PayrollPeriod::whereIn('status', ['approved', 'paid'])
            ->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->first();

        $lastPayrollTotal = $lastPayrollPeriod?->total_net ?? 0;
        $lastPayrollName = $lastPayrollPeriod?->period_name ?? 'N/A';

        // NEW: Top Products
        $topProducts = SaleItem::select('cylinder_type_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(total_price) as total_sales'))
            ->whereHas('sale', fn($q) => $q->where('status', 'approved'))
            ->groupBy('cylinder_type_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get()
            ->map(function($item) {
                $type = CylinderType::find($item->cylinder_type_id);
                return [
                    'name' => $type?->name ?? 'Unknown',
                    'qty' => $item->total_qty,
                    'sales' => $item->total_sales,
                ];
            });

        // NEW: Stock distribution for charts
        $stockDistribution = [
            'main_full' => $mainStoreStock->sum('full_qty'),
            'main_empty' => $mainStoreStock->sum('empty_qty'),
            'outlet_full' => StockOutlet::sum('full_qty'),
            'outlet_empty' => StockOutlet::sum('empty_qty'),
        ];

        return view('dashboard.index', compact(
            'totalFullCylinders',
            'totalEmptyCylinders',
            'pendingApprovals',
            'cashPendingReconciliation',
            'activeAssets',
            'stockByType',
            'outletStockSummary',
            'alerts',
            'recentActivity',
            'salesTodayCount',
            'salesTodayAmount',
            'salesWeekAmount',
            'salesMonthAmount',
            'fuelStocks',
            'pendingPOs',
            'pendingGRNs',
            'expensesMonth',
            'periodExpenses',
            'periodSalesCount',
            'periodSalesAmount',
            'periodProfit',
            'profitMargin',
            'monthProfit',
            'monthProfitMargin',
            'lastPayrollTotal',
            'lastPayrollName',
            'topProducts',
            'stockDistribution',
            'period',
            'periodLabel',
            'startDate',
            'endDate'
        ));
    }

    protected function getDateRange(string $period): array
    {
        $now = now();
        
        switch ($period) {
            case 'today':
                return [
                    'start' => $now->copy()->startOfDay(),
                    'end' => $now->copy()->endOfDay(),
                    'label' => 'Today',
                ];
            case 'yesterday':
                return [
                    'start' => $now->copy()->subDay()->startOfDay(),
                    'end' => $now->copy()->subDay()->endOfDay(),
                    'label' => 'Yesterday',
                ];
            case 'last_7_days':
                return [
                    'start' => $now->copy()->subDays(7)->startOfDay(),
                    'end' => $now->copy()->endOfDay(),
                    'label' => 'Last 7 Days',
                ];
            case 'last_30_days':
                return [
                    'start' => $now->copy()->subDays(30)->startOfDay(),
                    'end' => $now->copy()->endOfDay(),
                    'label' => 'Last 30 Days',
                ];
            case 'this_month':
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth(),
                    'label' => 'This Month',
                ];
            case 'last_month':
                return [
                    'start' => $now->copy()->subMonth()->startOfMonth(),
                    'end' => $now->copy()->subMonth()->endOfMonth(),
                    'label' => 'Last Month',
                ];
            case 'this_year':
                return [
                    'start' => $now->copy()->startOfYear(),
                    'end' => $now->copy()->endOfYear(),
                    'label' => 'This Year',
                ];
            case 'last_year':
                return [
                    'start' => $now->copy()->subYear()->startOfYear(),
                    'end' => $now->copy()->subYear()->endOfYear(),
                    'label' => 'Last Year',
                ];
            default:
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth(),
                    'label' => 'This Month',
                ];
        }
    }
}