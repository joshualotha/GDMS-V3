@extends('layouts.app')

@section('title', 'Sales Report')

@section('header', 'Sales Report')

@section('content')
<form method="GET" class="bg-white p-4 rounded-lg shadow mb-4 flex flex-wrap gap-4 items-end">
    <div>
        <label class="block text-xs text-gray-500">Date From</label>
        <input type="date" name="date_from" value="{{ request('date_from', date('Y-m-01')) }}" class="border rounded px-2 py-1">
    </div>
    <div>
        <label class="block text-xs text-gray-500">Date To</label>
        <input type="date" name="date_to" value="{{ request('date_to', date('Y-m-d')) }}" class="border rounded px-2 py-1">
    </div>
    <div>
        <label class="block text-xs text-gray-500">Outlet</label>
        <select name="outlet_id" class="border rounded px-2 py-1">
            <option value="">All Outlets</option>
            @foreach($outlets as $outlet)
                <option value="{{ $outlet->id }}" {{ request('outlet_id') == $outlet->id ? 'selected' : '' }}>{{ $outlet->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500">Cylinder Type</label>
        <select name="cylinder_type_id" class="border rounded px-2 py-1">
            <option value="">All Cylinders</option>
            @foreach($cylinderTypes as $ct)
                <option value="{{ $ct->id }}" {{ request('cylinder_type_id') == $ct->id ? 'selected' : '' }}>{{ $ct->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500">Status</label>
        <select name="status" class="border rounded px-2 py-1">
            <option value="">All Status</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="queried" {{ request('status') == 'queried' ? 'selected' : '' }}>Queried</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
    </div>
    <button type="submit" class="bg-gray-600 text-white px-4 py-1 rounded hover:bg-gray-700">Generate</button>
    <a href="{{ route('reports.sales.export', request()->query()) }}" class="bg-red-600 text-white px-4 py-1 rounded hover:bg-red-700">Export PDF</a>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Outlet</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cylinder</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Revenue</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">COGS</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Profit</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($saleItems as $item)
                <tr>
                    <td class="px-4 py-2">{{ $item->sale->sale_date->format('d/m/Y') }}</td>
                    <td class="px-4 py-2">{{ $item->sale->outlet->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $item->cylinderType->name ?? '-' }}</td>
                    <td class="px-4 py-2 text-right">{{ $item->quantity }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format($item->total_price, 2) }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format($item->total_cost, 2) }}</td>
                    <td class="px-4 py-2 text-right {{ $item->gross_profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($item->gross_profit, 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-4 text-center text-gray-500">No sales found.</td>
                </tr>
            @endforelse
        </tbody>
        @if($saleItems->count() > 0)
        <tfoot class="bg-gray-50 font-semibold">
            <tr>
                <td colspan="3" class="px-4 py-2">TOTALS</td>
                <td class="px-4 py-2 text-right">{{ $totals['total_qty'] }}</td>
                <td class="px-4 py-2 text-right">{{ number_format($totals['total_revenue'], 2) }}</td>
                <td class="px-4 py-2 text-right">{{ number_format($totals['total_cogs'], 2) }}</td>
                <td class="px-4 py-2 text-right {{ $totals['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ number_format($totals['total_profit'], 2) }}
                </td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
@endsection