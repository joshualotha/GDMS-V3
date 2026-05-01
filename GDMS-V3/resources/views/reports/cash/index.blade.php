@extends('layouts.app')

@section('title', 'Cash Reconciliation Report')

@section('header', 'Cash Reconciliation Report')

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
        <label class="block text-xs text-gray-500">Status</label>
        <select name="status" class="border rounded px-2 py-1">
            <option value="">All Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="queried" {{ request('status') == 'queried' ? 'selected' : '' }}>Queried</option>
        </select>
    </div>
    <button type="submit" class="bg-gray-600 text-white px-4 py-1 rounded hover:bg-gray-700">Generate</button>
    <a href="{{ route('reports.cash.export', request()->query()) }}" class="bg-red-600 text-white px-4 py-1 rounded hover:bg-red-700">Export PDF</a>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Outlet</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Sale #</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Expected Cash</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Submitted</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Variance</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($submissions as $sale)
                <tr>
                    <td class="px-4 py-2">{{ $sale->sale_date->format('d/m/Y') }}</td>
                    <td class="px-4 py-2">{{ $sale->outlet->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $sale->sale_number ?? '-' }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format($sale->total_amount, 2) }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format($sale->cash_submitted, 2) }}</td>
                    <td class="px-4 py-2 text-right {{ $sale->cash_variance != 0 ? 'text-red-600 font-bold' : 'text-green-600' }}">
                        {{ number_format($sale->cash_variance, 2) }}
                    </td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 text-xs rounded
                            {{ $sale->status == 'approved' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $sale->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $sale->status == 'queried' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($sale->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-4 text-center text-gray-500">No cash submissions found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection