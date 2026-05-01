@extends('layouts.app')

@section('title', 'Stock Adjustments')

@section('header', 'Stock Adjustments')

@section('content')
<div class="mb-4 flex justify-end">
    <a href="{{ url('warehouse/adjustments/create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
        New Adjustment
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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Adj #</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cylinder Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Full Change</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Empty Change</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($adjustments as $adj)
                <tr>
                    <td class="px-6 py-4">{{ $adj->adjustment_number }}</td>
                    <td class="px-6 py-4">{{ $adj->cylinderType->name }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded {{ $adj->type == 'gain' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($adj->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right {{ $adj->full_qty_change >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $adj->full_qty_change >= 0 ? '+' : '' }}{{ $adj->full_qty_change }}
                    </td>
                    <td class="px-6 py-4 text-right {{ $adj->empty_qty_change >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $adj->empty_qty_change >= 0 ? '+' : '' }}{{ $adj->empty_qty_change }}
                    </td>
                    <td class="px-6 py-4 text-xs">{{ $adj->reason }}</td>
                    <td class="px-6 py-4">{{ $adj->created_at->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No adjustments found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection