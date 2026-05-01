@extends('layouts.app')

@section('title', 'Sale Detail')

@section('header', 'Sale: ' . $sale->sale_number)

@section('content')
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="grid grid-cols-2 gap-6">
        <div><p class="text-sm text-gray-500">Outlet</p><p class="font-medium">{{ $sale->outlet->name }}</p></div>
        <div><p class="text-sm text-gray-500">Date</p><p class="font-medium">{{ $sale->sale_date->format('d/m/Y') }}</p></div>
        <div><p class="text-sm text-gray-500">Status</p><p class="font-medium">{{ ucfirst($sale->status) }}</p></div>
        <div><p class="text-sm text-gray-500">Expected Cash</p><p class="font-bold text-lg">{{ number_format($sale->total_price, 2) }}</p></div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cylinder</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Profit</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($sale->items as $item)
                <tr>
                    <td class="px-6 py-4">{{ $item->cylinderType->name }}</td>
                    <td class="px-6 py-4 capitalize">{{ $item->sale_type }}</td>
                    <td class="px-6 py-4 text-right">{{ $item->quantity }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($item->total_price, 2) }}</td>
                    <td class="px-6 py-4 text-right text-green-600">{{ number_format($item->gross_profit, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-gray-50">
            <tr>
                <td colspan="4" class="px-6 py-3 text-right font-medium">Total</td>
                <td class="px-6 py-3 text-right font-bold">{{ number_format($sale->total_price, 2) }}</td>
                <td class="px-6 py-3 text-right font-bold text-green-600">{{ number_format($sale->total_gross_profit, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="mt-6 flex justify-between">
    <a href="{{ url('sales') }}" class="btn btn-secondary">Back</a>
    @if($sale->status == 'pending')
        <form action="{{ url('approvals/sales/' . $sale->id . '/approve') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary">Approve & Verify</button>
        </form>
    @endif
</div>
@endsection