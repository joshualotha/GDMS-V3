@extends('layouts.app')

@section('title', 'Payroll Periods')

@section('header', 'Payroll Periods')

@section('content')
<div class="mb-4 flex justify-end">
    <a href="{{ route('payroll.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
        Generate Payroll
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Gross</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Deductions</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Net</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($periods as $period)
                <tr>
                    <td class="px-6 py-4">{{ $period->period_name }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($period->total_gross, 2) }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($period->total_deductions, 2) }}</td>
                    <td class="px-6 py-4 text-right font-semibold">{{ number_format($period->total_net, 2) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded 
                            {{ $period->status == 'draft' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $period->status == 'approved' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $period->status == 'paid' ? 'bg-green-100 text-green-800' : '' }}">
                            {{ ucfirst($period->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 flex gap-2">
                        <a href="{{ route('payroll.show', $period) }}" class="text-indigo-600 hover:underline">View</a>
                        @if($period->status == 'draft')
                        <form action="{{ route('payroll.approve', $period) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-blue-600 hover:underline" onclick="return confirm('Approve this payroll period?')">Approve</button>
                        </form>
                        @elseif($period->status == 'approved')
                        <form action="{{ route('payroll.markPaid', $period) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-green-600 hover:underline" onclick="return confirm('Mark this payroll as paid?')">Mark Paid</button>
                        </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No payroll periods found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection