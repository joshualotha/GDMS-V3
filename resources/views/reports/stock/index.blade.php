@extends('layouts.app')

@section('title', 'Stock Report')

@section('header', 'Stock Report')

@section('content')
<form method="GET" class="bg-white p-4 rounded-lg shadow mb-4 flex flex-wrap gap-4 items-end">
    <div>
        <label class="block text-xs text-gray-500">As at Date</label>
        <input type="date" name="date" value="{{ request('date', date('Y-m-d')) }}" class="border rounded px-2 py-1">
    </div>
    <button type="submit" class="bg-gray-600 text-white px-4 py-1 rounded hover:bg-gray-700">Generate</button>
    <a href="{{ route('reports.stock.export', request()->query()) }}" class="bg-red-600 text-white px-4 py-1 rounded hover:bg-red-700">Export PDF</a>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden mb-6">
    <div class="bg-gray-50 px-6 py-3 font-semibold">Main Store Stock</div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cylinder Type</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Full Cylinders</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Empty Cylinders</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($cylinderTypes as $ct)
                <tr>
                    <td class="px-4 py-2">{{ $ct->name }}</td>
                    <td class="px-4 py-2 text-right">{{ $mainStock[$ct->id]['full'] ?? 0 }}</td>
                    <td class="px-4 py-2 text-right">{{ $mainStock[$ct->id]['empty'] ?? 0 }}</td>
                    <td class="px-4 py-2 text-right font-semibold">{{ ($mainStock[$ct->id]['full'] ?? 0) + ($mainStock[$ct->id]['empty'] ?? 0) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-gray-50 font-semibold">
            <tr>
                <td class="px-4 py-2">TOTAL</td>
                <td class="px-4 py-2 text-right">{{ $totalFull }}</td>
                <td class="px-4 py-2 text-right">{{ $totalEmpty }}</td>
                <td class="px-4 py-2 text-right">{{ $totalFull + $totalEmpty }}</td>
            </tr>
        </tfoot>
    </table>
</div>

@foreach($outlets as $outlet)
<div class="bg-white rounded-lg shadow overflow-hidden mb-6">
    <div class="bg-gray-50 px-6 py-3 font-semibold">{{ $outlet->name }} - Stock</div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cylinder Type</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Full</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Empty</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($cylinderTypes as $ct)
                <tr>
                    <td class="px-4 py-2">{{ $ct->name }}</td>
                    <td class="px-4 py-2 text-right">{{ $outletStock[$outlet->id][$ct->id]['full'] ?? 0 }}</td>
                    <td class="px-4 py-2 text-right">{{ $outletStock[$outlet->id][$ct->id]['empty'] ?? 0 }}</td>
                    <td class="px-4 py-2 text-right font-semibold">{{ ($outletStock[$outlet->id][$ct->id]['full'] ?? 0) + ($outletStock[$outlet->id][$ct->id]['empty'] ?? 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endforeach
@endsection