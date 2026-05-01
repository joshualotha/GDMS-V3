@extends('layouts.app')

@section('title', 'Goods Received')

@section('header', 'Goods Received')

@section('content')
<div class="mb-4 flex justify-end">
    <a href="{{ route('goods-received.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
        New GRN
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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">GRN Number</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PO Number</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Cost</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($goodsReceived as $grn)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $grn->grn_number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $grn->supplier->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $grn->purchaseOrder->po_number ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($grn->total_cost, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($grn->status == 'pending')
                            <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Pending</span>
                        @elseif($grn->status == 'completed')
                            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Completed</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">Cancelled</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $grn->created_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('goods-received.show', $grn) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No goods received found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection