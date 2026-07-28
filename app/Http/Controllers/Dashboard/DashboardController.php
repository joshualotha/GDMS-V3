<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CylinderType;
use App\Models\Outlet;
use App\Models\StockMain;
use App\Models\StockOutlet;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\CompanyAsset;
use App\Models\StockTransfer;
use App\Models\GoodsReceived;
use App\Models\FuelStock;
use App\Models\Expense;
use App\Models\PayrollPeriod;
use App\Models\Supplier;
use App\Models\OpeningStock;
use App\Models\AssetCategory;
use App\Models\ExpenseCategory;
use App\Models\Employee;
use App\Models\Setting;
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

        $pendingApprovals = Sale::whereIn('status', ['pending', 'queried'])->count();

        $cashPendingReconciliation = Sale::where('status', 'pending')
            ->whereNotNull('cash_submitted')
            ->sum('cash_submitted');

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

            $pendingSubmission = Sale::where('outlet_id', $outlet->id)
                ->where('status', 'pending')
                ->whereNotNull('cash_submitted')
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


        $varianceSales = Sale::where('status', 'approved')
            ->where('cash_variance', '!=', 0)
            ->orderBy('cash_submitted_date', 'desc')
            ->limit(5)
            ->get();

        foreach ($varianceSales as $sale) {
            $alerts[] = [
                'type' => 'orange',
                'message' => "Sale {$sale->sale_number} has a cash variance of " . number_format($sale->cash_variance, 2)
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

        // Profit calculation
        $periodProfit = $periodSalesAmount - $periodExpenses;
        $profitMargin = $periodSalesAmount > 0 ? round(($periodProfit / $periodSalesAmount) * 100, 1) : 0;

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

        $setupChecklist = $this->buildSetupChecklist();

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
            'fuelStocks',
            'setupChecklist',
            'periodExpenses',
            'periodSalesCount',
            'periodSalesAmount',
            'periodProfit',
            'profitMargin',
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

    /**
     * Setup items a new business should fill in before relying on the system day to day.
     * "required" items hide the whole checklist once complete; "optional" ones stay
     * listed (so the manager knows they exist) but don't block the checklist from disappearing.
     */
    protected function buildSetupChecklist(): array
    {
        $businessNameSet = trim((string) Setting::get('business_name', '')) !== '';

        $items = [
            ['label' => 'Cylinder Types', 'desc' => 'Sizes, full-sale and refill prices', 'done' => CylinderType::count() > 0, 'url' => url('settings/cylinder-types'), 'required' => true],
            ['label' => 'Outlets', 'desc' => 'Physical locations and vehicles that sell', 'done' => Outlet::count() > 0, 'url' => url('settings/outlets'), 'required' => true],
            ['label' => 'Suppliers', 'desc' => 'Who you buy cylinders and fuel from', 'done' => Supplier::count() > 0, 'url' => url('settings/suppliers'), 'required' => true],
            ['label' => 'Opening Stock', 'desc' => 'Starting cylinder counts for warehouse and outlets', 'done' => OpeningStock::count() > 0, 'url' => url('warehouse/opening-stock'), 'required' => true],
            ['label' => 'General Settings', 'desc' => 'Business name, currency, financial year', 'done' => $businessNameSet, 'url' => url('settings/general'), 'required' => true],
            ['label' => 'Asset Categories', 'desc' => 'Only needed if you track vehicles/equipment', 'done' => AssetCategory::count() > 0, 'url' => url('settings/asset-categories'), 'required' => false],
            ['label' => 'Expense Categories', 'desc' => 'Only needed if you record expenses', 'done' => ExpenseCategory::count() > 0, 'url' => url('settings/expense-categories'), 'required' => false],
            ['label' => 'Employees', 'desc' => 'Only needed if you run payroll', 'done' => Employee::count() > 0, 'url' => url('hr/employees'), 'required' => false],
            ['label' => 'Company Assets', 'desc' => 'Vehicles/equipment you already own', 'done' => CompanyAsset::count() > 0, 'url' => url('assets'), 'required' => false],
            ['label' => 'Fuel Stock', 'desc' => 'Only needed if you track vehicle fuel', 'done' => FuelStock::sum('litres') > 0, 'url' => url('fuel/purchases'), 'required' => false],
        ];

        $requiredIncomplete = collect($items)->where('required', true)->where('done', false)->count();

        return [
            'items' => $items,
            'visible' => $requiredIncomplete > 0,
        ];
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