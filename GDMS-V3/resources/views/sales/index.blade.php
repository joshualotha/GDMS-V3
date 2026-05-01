@extends('layouts.app')

@section('title', 'Sales')

@section('header', 'Sales')

@section('content')
<div class="mb-4 flex justify-between items-center">
    <form method="GET" class="flex gap-4 items-center">
        <select name="outlet_id" class="rounded border-gray-300">
            <option value="">All Outlets</option>
            @foreach($outlets as $outlet)
                <option value="{{ $outlet->id }}" {{ request()->outlet_id == $outlet->id ? 'selected' : '' }}>{{ $outlet->name }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded border-gray-300">
            <option value="">All Status</option>
            <option value="pending" {{ request()->status == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request()->status == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="queried" {{ request()->status == 'queried' ? 'selected' : '' }}>Queried</option>
        </select>
        <input type="date" name="date_from" value="{{ request()->date_from }}" class="rounded border-gray-300">
        <input type="date" name="date_to" value="{{ request()->date_to }}" class="rounded border-gray-300">
        <button type="submit" class="bg-indigo-600 text-white px-3 py-1 rounded">Filter</button>
    </form>
    <a href="{{ url('sales/create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">New Sale</a>
</div>

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
@endif

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sale #</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Outlet</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Profit</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($sales as $sale)
                <tr>
                    <td class="px-6 py-4">{{ $sale->sale_number }}</td>
                    <td class="px-6 py-4">{{ $sale->outlet->name }}</td>
                    <td class="px-6 py-4">{{ $sale->sale_date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($sale->total_price, 2) }}</td>
                    <td class="px-6 py-4 text-right text-green-600">{{ number_format($sale->total_gross_profit, 2) }}</td>
                    <td class="px-6 py-4">
                        @if($sale->status == 'pending')
                            <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Pending</span>
                        @elseif($sale->status == 'approved')
                            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Approved</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">Queried</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ url('sales/' . $sale->id) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No sales found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection