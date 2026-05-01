@extends('layouts.app')

@section('title', 'Stock Movement Report')

@section('header', 'Stock Movement Report')

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
        <label class="block text-xs text-gray-500">Transaction Type</label>
        <select name="transaction_type" class="border rounded px-2 py-1">
            <option value="">All Types</option>
            <option value="opening" {{ request('transaction_type') == 'opening' ? 'selected' : '' }}>Opening Stock</option>
            <option value="purchase" {{ request('transaction_type') == 'purchase' ? 'selected' : '' }}>Purchase</option>
            <option value="transfer" {{ request('transaction_type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
            <option value="sale" {{ request('transaction_type') == 'sale' ? 'selected' : '' }}>Sale</option>
            <option value="return" {{ request('transaction_type') == 'return' ? 'selected' : '' }}>Return</option>
            <option value="adjustment" {{ request('transaction_type') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
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
    <button type="submit" class="bg-gray-600 text-white px-4 py-1 rounded hover:bg-gray-700">Generate</button>
    <a href="{{ route('reports.stock-movement.export', request()->query()) }}" class="bg-red-600 text-white px-4 py-1 rounded hover:bg-red-700">Export PDF</a>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cylinder</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Full Change</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Empty Change</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Full After</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Empty After</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($movements as $movement)
                <tr>
                    <td class="px-4 py-2">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-2">{{ ucfirst($movement->transaction_type) }}</td>
                    <td class="px-4 py-2">{{ $movement->cylinderType->name ?? '-' }}</td>
                    <td class="px-4 py-2 text-right {{ $movement->full_qty_change >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $movement->full_qty_change >= 0 ? '+' : '' }}{{ $movement->full_qty_change }}
                    </td>
                    <td class="px-4 py-2 text-right {{ $movement->empty_qty_change >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $movement->empty_qty_change >= 0 ? '+' : '' }}{{ $movement->empty_qty_change }}
                    </td>
                    <td class="px-4 py-2 text-right">{{ $movement->full_qty_after }}</td>
                    <td class="px-4 py-2 text-right">{{ $movement->empty_qty_after }}</td>
                    <td class="px-4 py-2 text-xs text-gray-500">{{ $movement->reference_type ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-4 text-center text-gray-500">No movements found.</td>
                </tr>
            @endforelse
        </tbody>
        @if($movements->count() > 0)
        <tfoot class="bg-gray-50 font-semibold">
            <tr>
                <td colspan="3" class="px-4 py-2">TOTALS</td>
                <td class="px-4 py-2 text-right {{ $totals['full_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $totals['full_change'] >= 0 ? '+' : '' }}{{ $totals['full_change'] }}
                </td>
                <td class="px-4 py-2 text-right {{ $totals['empty_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $totals['empty_change'] >= 0 ? '+' : '' }}{{ $totals['empty_change'] }}
                </td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
@endsection