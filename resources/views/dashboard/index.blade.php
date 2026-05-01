@extends('layouts.app')

@section('title', 'Dashboard')

@section('header', 'Dashboard')
@section('breadcrumb', 'Overview of your gas distribution business')

@section('content')
{{-- Period Filter --}}
<div class="flex justify-between items-center mb-6">
    <div class="flex gap-3 items-center">
        <form method="GET" action="{{ url('dashboard') }}" class="flex gap-3 items-center">
            <label style="font-size: 14px; font-weight: 500;">Period:</label>
            <select name="period" class="form-select" style="width: auto; height: 40px; padding: 0 32px 0 12px;" onchange="this.form.submit()">
                <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Today</option>
                <option value="yesterday" {{ $period == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                <option value="last_7_days" {{ $period == 'last_7_days' ? 'selected' : '' }}>Last 7 Days</option>
                <option value="last_30_days" {{ $period == 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                <option value="this_month" {{ $period == 'this_month' ? 'selected' : '' }}>This Month</option>
                <option value="last_month" {{ $period == 'last_month' ? 'selected' : '' }}>Last Month</option>
                <option value="this_year" {{ $period == 'this_year' ? 'selected' : '' }}>This Year</option>
                <option value="last_year" {{ $period == 'last_year' ? 'selected' : '' }}>Last Year</option>
            </select>
        </form>
        <span class="badge badge-info">{{ $periodLabel }}</span>
    </div>
</div>

{{-- Quick Actions --}}
<div class="flex gap-3 mb-6">
    <a href="{{ url('sales/create') }}" class="btn btn-primary btn-sm">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
        New Sale
    </a>
    <a href="{{ url('procurement/purchase-orders/create') }}" class="btn btn-secondary btn-sm">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        New PO
    </a>
    <a href="{{ url('warehouse/movements') }}" class="btn btn-secondary btn-sm">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17l5-5 5 5M7 7l5 5 5-5"/></svg>
        Stock Movement
    </a>
</div>

{{-- KPI Cards --}}
<div class="grid-4 mb-6">
    <div class="stat-card">
        <div class="stat-label">Full Cylinders</div>
        <div class="stat-value">{{ number_format($totalFullCylinders) }}</div>
        <div class="stat-sublabel">In Main Store</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Pending Approvals</div>
        <div class="stat-value" style="color: var(--primary);">{{ number_format($pendingApprovals) }}</div>
        <div class="stat-sublabel">Sales & Cash</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Cash Pending</div>
        <div class="stat-value" style="color: var(--warning);">{{ number_format($cashPendingReconciliation, 2) }}</div>
        <div class="stat-sublabel">Reconciliation</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Active Assets</div>
        <div class="stat-value" style="color: var(--success);">{{ number_format($activeAssets) }}</div>
        <div class="stat-sublabel">In System</div>
    </div>
</div>

{{-- Sales Metrics --}}
<div class="mb-6">
    <h2 class="mb-4">Sales Performance</h2>
    <div class="grid-4">
        <div class="stat-card">
            <div class="stat-label">Sales ({{ $periodLabel }})</div>
            <div class="stat-value" style="color: var(--success);">{{ number_format($periodSalesCount) }}</div>
            <div class="stat-sublabel">{{ number_format($periodSalesAmount, 2) }} revenue</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Profit ({{ $periodLabel }})</div>
            <div class="stat-value" style="color: {{ $periodProfit >= 0 ? 'var(--success)' : 'var(--danger)' }};">{{ number_format($periodProfit, 2) }}</div>
            <div class="stat-sublabel">{{ $profitMargin }}% margin</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">This Month Profit</div>
            <div class="stat-value" style="color: {{ $monthProfit >= 0 ? 'var(--success)' : 'var(--danger)' }};">{{ number_format($monthProfit, 2) }}</div>
            <div class="stat-sublabel">{{ $monthProfitMargin }}% margin</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Pending Tasks</div>
            <div class="stat-value" style="color: var(--warning);">{{ $pendingPOs + $pendingGRNs }}</div>
            <div class="stat-sublabel">POs: {{ $pendingPOs }} | GRNs: {{ $pendingGRNs }}</div>
        </div>
    </div>
</div>

{{-- Finance & Expenses --}}
<div class="grid-4 mb-6">
    <div class="stat-card">
        <div class="stat-label">Expenses ({{ $periodLabel }})</div>
        <div class="stat-value" style="color: var(--danger);">{{ number_format($periodExpenses, 2) }}</div>
        <div class="stat-sublabel">Total incurred</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Expenses (Month)</div>
        <div class="stat-value" style="color: var(--danger);">{{ number_format($expensesMonth, 2) }}</div>
        <div class="stat-sublabel">This month</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Last Payroll</div>
        <div class="stat-value">{{ number_format($lastPayrollTotal, 2) }}</div>
        <div class="stat-sublabel">{{ $lastPayrollName }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Diesel Stock</div>
        <div class="stat-value" style="color: var(--info);">{{ number_format($fuelStocks['Diesel'] ?? 0) }}</div>
        <div class="stat-sublabel">Litres</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Petrol Stock</div>
        <div class="stat-value" style="color: var(--info);">{{ number_format($fuelStocks['Petrol'] ?? 0) }}</div>
        <div class="stat-sublabel">Litres</div>
    </div>
</div>

{{-- Charts Row --}}
<div class="grid-2 mb-6">
    <div class="card">
        <div class="card-header">
            <h3>Stock Distribution</h3>
        </div>
        <div class="card-body">
            @php
            $totalStock = $stockDistribution['main_full'] + $stockDistribution['main_empty'] + $stockDistribution['outlet_full'] + $stockDistribution['outlet_empty'];
            $mainFullPct = $totalStock > 0 ? round(($stockDistribution['main_full'] / $totalStock) * 100) : 0;
            $mainEmptyPct = $totalStock > 0 ? round(($stockDistribution['main_empty'] / $totalStock) * 100) : 0;
            $outletFullPct = $totalStock > 0 ? round(($stockDistribution['outlet_full'] / $totalStock) * 100) : 0;
            $outletEmptyPct = $totalStock > 0 ? round(($stockDistribution['outlet_empty'] / $totalStock) * 100) : 0;
            @endphp
            <div style="display: flex; height: 24px; border-radius: 6px; overflow: hidden; margin-bottom: 16px;">
                <div style="background: var(--success); width: {{ $mainFullPct }}%;" title="Main Store Full: {{ $stockDistribution['main_full'] }}"></div>
                <div style="background: var(--text-muted); width: {{ $mainEmptyPct }}%;" title="Main Store Empty: {{ $stockDistribution['main_empty'] }}"></div>
                <div style="background: var(--primary); width: {{ $outletFullPct }}%;" title="Outlet Full: {{ $stockDistribution['outlet_full'] }}"></div>
                <div style="background: var(--border-strong); width: {{ $outletEmptyPct }}%;" title="Outlet Empty: {{ $stockDistribution['outlet_empty'] }}"></div>
            </div>
            <div style="display: flex; gap: 16px; flex-wrap: wrap; font-size: 13px;">
                <div style="display: flex; align-items: center; gap: 6px;"><span style="width: 12px; height: 12px; background: var(--success); border-radius: 2px;"></span> Main Full ({{ $stockDistribution['main_full'] }})</div>
                <div style="display: flex; align-items: center; gap: 6px;"><span style="width: 12px; height: 12px; background: var(--text-muted); border-radius: 2px;"></span> Main Empty ({{ $stockDistribution['main_empty'] }})</div>
                <div style="display: flex; align-items: center; gap: 6px;"><span style="width: 12px; height: 12px; background: var(--primary); border-radius: 2px;"></span> Outlet Full ({{ $stockDistribution['outlet_full'] }})</div>
                <div style="display: flex; align-items: center; gap: 6px;"><span style="width: 12px; height: 12px; background: var(--border-strong); border-radius: 2px;"></span> Outlet Empty ({{ $stockDistribution['outlet_empty'] }})</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Top Products</h3>
        </div>
        <table style="font-size: 13px;">
            <thead>
                <tr>
                    <th>Product</th>
                    <th class="text-right">Qty Sold</th>
                    <th class="text-right">Sales</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topProducts as $product)
                <tr>
                    <td>{{ $product['name'] }}</td>
                    <td class="text-right">{{ number_format($product['qty']) }}</td>
                    <td class="text-right">{{ number_format($product['sales'], 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center">No sales data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Stock Summary Table --}}
<div class="card mb-6">
    <div class="card-header">
        <h3>Stock Summary by Cylinder Type</h3>
    </div>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Cylinder Type</th>
                    <th class="text-right">Main Full</th>
                    <th class="text-right">Main Empty</th>
                    <th class="text-right">Outlet Full</th>
                    <th class="text-right">Outlet Empty</th>
                    <th class="text-right">Total Full</th>
                    <th class="text-right">Total Empty</th>
                    <th class="text-right">Reorder Level</th>
                    <th class="text-right">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stockByType as $stock)
                    @php
                        $isLowStock = $stock['total_full'] > 0 && $stock['total_full'] <= $stock['reorder_level'];
                        $isOutOfStock = $stock['total_full'] == 0;
                    @endphp
                    <tr>
                        <td>{{ $stock['type'] }}</td>
                        <td class="text-right">{{ number_format($stock['main_full']) }}</td>
                        <td class="text-right">{{ number_format($stock['main_empty']) }}</td>
                        <td class="text-right">{{ number_format($stock['outlet_full']) }}</td>
                        <td class="text-right">{{ number_format($stock['outlet_empty']) }}</td>
                        <td class="text-right font-semibold" style="{{ $isLowStock ? 'color: var(--warning);' : '' }}">{{ number_format($stock['total_full']) }}</td>
                        <td class="text-right">{{ number_format($stock['total_empty']) }}</td>
                        <td class="text-right">{{ $stock['reorder_level'] }}</td>
                        <td>
                            @if($isOutOfStock)
                            <span class="badge badge-danger">Out of Stock</span>
                            @elseif($isLowStock)
                            <span class="badge badge-warning">Low Stock</span>
                            @else
                            <span class="badge badge-success">OK</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">No cylinder types configured.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Outlet Status Cards --}}
<div class="mb-6">
    <h2 class="mb-4">Outlet Status</h2>
    <div class="grid-3">
        @forelse($outletStockSummary as $summary)
            <div class="card">
                <div class="card-body">
                    <div class="flex justify-between items-center mb-2">
                        <h3>{{ $summary['outlet']->name }}</h3>
                        <span class="badge badge-info">{{ $summary['outlet']->type }}</span>
                    </div>
                    <div class="stat-value">{{ number_format($summary['full_qty']) }}</div>
                    <div class="text-sm" style="color: var(--text-muted);">full cylinders</div>
                    <div class="text-sm mt-2" style="color: var(--text-muted);">
                        @if($summary['last_sale_date'])
                            Last sale: {{ $summary['last_sale_date']->format('d M Y') }}
                        @else
                            No sales yet
                        @endif
                    </div>
                    @if($summary['pending_submission'])
                        <div class="mt-2 badge badge-warning">Pending Submission</div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full text-center">No outlets configured.</div>
        @endforelse
    </div>
</div>

{{-- Alerts Section --}}
@if(count($alerts) > 0)
<div class="card mb-6">
    <div class="card-header">
        <h3>Alerts</h3>
    </div>
    <div class="card-body" style="display: flex; flex-direction: column; gap: 12px;">
        @foreach($alerts as $alert)
            <div class="alert 
                {{ $alert['type'] == 'danger' ? 'alert-danger' : '' }}
                {{ $alert['type'] == 'warning' ? 'alert-warning' : '' }}
                {{ $alert['type'] == 'orange' ? 'alert-warning' : '' }}">
                {{ $alert['message'] }}
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- Recent Activity Feed --}}
<div class="card">
    <div class="card-header">
        <h3>Recent Activity</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Reference</th>
                <th>Outlet</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentActivity as $activity)
                <tr>
                    <td>{{ $activity['type'] }}</td>
                    <td>{{ $activity['ref'] }}</td>
                    <td>{{ $activity['outlet'] ?? '-' }}</td>
                    <td>{{ $activity['date']->format('d M Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No recent activity.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection