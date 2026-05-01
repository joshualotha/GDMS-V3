@extends('layouts.app')

@section('title', 'Procurement Report')

@section('header', 'Procurement Report')

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
        <label class="block text-xs text-gray-500">Supplier</label>
        <select name="supplier_id" class="border rounded px-2 py-1">
            <option value="">All Suppliers</option>
            @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
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
    <button type="submit" class="bg-gray-600 text-white px-4 py-1 rounded hover:bg-gray-700">Generate</button>
    <a href="{{ route('reports.procurement.export', request()->query()) }}" class="bg-red-600 text-white px-4 py-1 rounded hover:bg-red-700">Export PDF</a>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">GR Number</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cylinder</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Unit Cost</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total Cost</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($items as $item)
                <tr>
                    <td class="px-4 py-2">{{ $item->goodsReceived->received_date ? $item->goodsReceived->received_date->format('d/m/Y') : '-' }}</td>
                    <td class="px-4 py-2">{{ $item->goodsReceived->gr_number ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $item->goodsReceived->supplier->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $item->cylinderType->name ?? '-' }}</td>
                    <td class="px-4 py-2 text-right">{{ $item->quantity }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format($item->unit_cost, 2) }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format($item->line_total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-4 text-center text-gray-500">No procurements found.</td>
                </tr>
            @endforelse
        </tbody>
        @if($items->count() > 0)
        <tfoot class="bg-gray-50 font-semibold">
            <tr>
                <td colspan="4" class="px-4 py-2">TOTALS</td>
                <td class="px-4 py-2 text-right">{{ $totalQty }}</td>
                <td></td>
                <td class="px-4 py-2 text-right">{{ number_format($totalCost, 2) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
@endsection