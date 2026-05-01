@extends('layouts.app')

@section('title', 'Cash Submissions')

@section('header', 'Cash Submissions')

@section('content')
<div class="mb-4 flex justify-end">
    <a href="{{ url('sales') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Back to Sales</a>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ref</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sale #</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Outlet</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Expected</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Submitted</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Variance</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($submissions as $sub)
                <tr>
                    <td class="px-6 py-4">{{ $sub->reference }}</td>
                    <td class="px-6 py-4">{{ $sub->sale->sale_number }}</td>
                    <td class="px-6 py-4">{{ $sub->sale->outlet->name }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($sub->expected_amount, 2) }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($sub->submitted_amount, 2) }}</td>
                    <td class="px-6 py-4 text-right {{ $sub->variance >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($sub->variance, 2) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded {{ $sub->status == 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ ucfirst($sub->status) }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ url('cash-submissions/' . $sub->id) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="px-6 py-4 text-center text-gray-500">No submissions found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection