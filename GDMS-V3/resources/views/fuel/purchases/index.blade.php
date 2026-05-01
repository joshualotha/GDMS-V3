@extends('layouts.app')

@section('title', 'Fuel Purchases')

@section('header', 'Fuel Purchases')

@section('content')
<div class="mb-4 flex justify-end gap-4">
    <a href="{{ url('fuel/stock') }}" class="bg-gray-600 text-white px-4 py-2 rounded">Stock</a>
    <a href="{{ url('fuel/purchases/create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded">New Purchase</a>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fuel Type</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Litres</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unit Cost</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($purchases as $purchase)
                <tr>
                    <td class="px-6 py-4">{{ $purchase->date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 capitalize">{{ $purchase->fuel_type }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($purchase->litres, 2) }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($purchase->unit_cost, 2) }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($purchase->total_cost, 2) }}</td>
                    <td class="px-6 py-4">{{ $purchase->supplier ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No purchases found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection