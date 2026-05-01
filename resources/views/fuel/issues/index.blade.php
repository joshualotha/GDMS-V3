@extends('layouts.app')

@section('title', 'Fuel Issues')

@section('header', 'Fuel Issues')

@section('content')
<div class="mb-4 flex justify-end gap-4">
    <a href="{{ url('fuel/stock') }}" class="bg-gray-600 text-white px-4 py-2 rounded">Stock</a>
    <a href="{{ url('fuel/issues/create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded">New Issue</a>
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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asset</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fuel Type</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Litres</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Odometer</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Issued By</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($issues as $issue)
                <tr>
                    <td class="px-6 py-4">{{ $issue->date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">{{ $issue->asset->name }}</td>
                    <td class="px-6 py-4 capitalize">{{ $issue->fuel_type }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($issue->litres, 2) }}</td>
                    <td class="px-6 py-4 text-right">{{ $issue->odometer_km ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $issue->issued_by ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No issues found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection