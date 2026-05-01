@extends('layouts.app')

@section('title', 'Purchase Order')

@section('header', 'Purchase Order: ' . $purchaseOrder->po_number)

@section('content')
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="grid grid-cols-2 gap-6">
        <div>
            <p class="text-sm text-gray-500">Supplier</p>
            <p class="font-medium">{{ $purchaseOrder->supplier->name }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Status</p>
            <p>
                @if($purchaseOrder->status == 'pending')
                    <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Pending</span>
                @elseif($purchaseOrder->status == 'received')
                    <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Received</span>
                @else
                    <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">Cancelled</span>
                @endif
            </p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Date</p>
            <p class="font-medium">{{ $purchaseOrder->created_at->format('d/m/Y') }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Total Cost</p>
            <p class="font-medium">{{ number_format($purchaseOrder->total_cost, 2) }}</p>
        </div>
    </div>
    @if($purchaseOrder->notes)
        <div class="mt-4">
            <p class="text-sm text-gray-500">Notes</p>
            <p>{{ $purchaseOrder->notes }}</p>
        </div>
    @endif
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cylinder Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unit Cost</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($purchaseOrder->items as $item)
                <tr>
                    <td class="px-6 py-4">{{ $item->cylinderType->name }} ({{ $item->cylinderType->size_kg }}kg)</td>
                    <td class="px-6 py-4 capitalize">{{ $item->purchase_type }}</td>
                    <td class="px-6 py-4 text-right">{{ $item->quantity }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($item->unit_cost, 2) }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-gray-50">
            <tr>
                <td colspan="4" class="px-6 py-3 text-right font-medium">Total</td>
                <td class="px-6 py-3 text-right font-bold">{{ number_format($purchaseOrder->total_cost, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="mt-6 flex justify-between">
    <a href="{{ route('purchase-orders.index') }}" class="px-4 py-2 text-gray-700 border border-gray-300 rounded hover:bg-gray-50">
        Back
    </a>
    @if($purchaseOrder->status == 'pending')
        <a href="{{ route('goods-received.create', ['po_id' => $purchaseOrder->id]) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            Receive Goods
        </a>
    @endif
</div>
@endsection