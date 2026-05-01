@extends('layouts.app')

@section('title', 'Fuel Report')

@section('header', 'Fuel Report')

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
        <label class="block text-xs text-gray-500">Fuel Type</label>
        <select name="fuel_type" class="border rounded px-2 py-1">
            <option value="">All Types</option>
            <option value="diesel" {{ request('fuel_type') == 'diesel' ? 'selected' : '' }}>Diesel</option>
            <option value="petrol" {{ request('fuel_type') == 'petrol' ? 'selected' : '' }}>Petrol</option>
        </select>
    </div>
    <button type="submit" class="bg-gray-600 text-white px-4 py-1 rounded hover:bg-gray-700">Generate</button>
    <a href="{{ route('reports.fuel.export', request()->query()) }}" class="bg-red-600 text-white px-4 py-1 rounded hover:bg-red-700">Export PDF</a>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden mb-6">
    <div class="bg-gray-50 px-6 py-3 font-semibold">Section 1: Purchases</div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fuel Type</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Litres</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Cost</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($purchases as $purchase)
                <tr>
                    <td class="px-4 py-2">{{ $purchase->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-2">{{ ucfirst($purchase->fuel_type) }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format($purchase->litres, 2) }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format($purchase->total_cost, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-4 text-center text-gray-500">No purchases found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden mb-6">
    <div class="bg-gray-50 px-6 py-3 font-semibold">Section 2: Issues</div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Vehicle</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fuel Type</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Litres</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Odometer</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($issues as $issue)
                <tr>
                    <td class="px-4 py-2">{{ $issue->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-2">{{ $issue->asset->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ ucfirst($issue->fuel_type) }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format($issue->litres, 2) }}</td>
                    <td class="px-4 py-2 text-right">{{ $issue->odometer_km ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-4 text-center text-gray-500">No issues found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="bg-gray-50 px-6 py-3 font-semibold">Section 3: Balance Summary</div>
    <table class="min-w-full">
        <tbody>
            <tr class="border-b">
                <td class="px-4 py-2">Diesel Balance</td>
                <td class="px-4 py-2 text-right font-semibold">{{ number_format($balance['diesel'], 2) }} L</td>
            </tr>
            <tr>
                <td class="px-4 py-2">Petrol Balance</td>
                <td class="px-4 py-2 text-right font-semibold">{{ number_format($balance['petrol'], 2) }} L</td>
            </tr>
        </tbody>
    </table>
</div>
@endsection