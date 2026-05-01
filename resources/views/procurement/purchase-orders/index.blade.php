@extends('layouts.app')

@section('title', 'Purchase Orders')

@section('header', ' Purchase Orders')

@section('content')
<div class="mb-4 flex justify-end">
    <a href="{{ route('purchase-orders.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
        New Purchase Order
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PO Number</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Cost</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($purchaseOrders as $po)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $po->po_number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $po->supplier->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($po->total_cost, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($po->status == 'pending')
                            <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Pending</span>
                        @elseif($po->status == 'received')
                            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Received</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">Cancelled</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $po->created_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('purchase-orders.show', $po) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No purchase orders found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection