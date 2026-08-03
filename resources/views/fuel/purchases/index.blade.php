@extends('layouts.app')

@section('title', 'Fuel Purchases')

@section('header', 'Fuel Purchases')

@section('content')
<div class="mb-4 flex justify-end gap-4">
    <a href="{{ url('fuel/stock') }}" class="bg-gray-600 text-white px-4 py-2 rounded">Stock</a>
    <a href="{{ url('fuel/purchases/create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded">New Purchase</a>
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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vehicle</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Odometer</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fuel Type</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Litres</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unit Cost</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount Paid</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($purchases as $purchase)
                <tr>
                    <td class="px-6 py-4">{{ $purchase->date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">{{ $purchase->outlet->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-right">{{ $purchase->odometer_km !== null ? number_format($purchase->odometer_km) . ' km' : '-' }}</td>
                    <td class="px-6 py-4 capitalize">{{ $purchase->fuel_type }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($purchase->litres, 2) }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($purchase->unit_cost, 2) }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($purchase->total_cost, 2) }}</td>
                    <td class="px-6 py-4">{{ $purchase->supplierAccount->name ?? $purchase->supplier ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <form action="{{ route('fuel.purchases.destroy', $purchase) }}" method="POST" onsubmit="return confirm('Delete this fuel purchase? This removes its auto-generated expense and, for legacy bulk purchases, reverses stock.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline text-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="px-6 py-4 text-center text-gray-500">No purchases found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection