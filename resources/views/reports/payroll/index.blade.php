@extends('layouts.app')

@section('title', 'Payroll Report')

@section('header', 'Payroll Report')

@section('content')
<form method="GET" class="bg-white p-4 rounded-lg shadow mb-4 flex flex-wrap gap-4 items-end">
    <div>
        <label class="block text-xs text-gray-500">Payroll Period</label>
        <select name="period_id" class="border rounded px-2 py-1">
            <option value="">Select Period</option>
            @foreach($periods as $period)
                <option value="{{ $period->id }}" {{ request('period_id') == $period->id ? 'selected' : '' }}>
                    {{ $period->period_name }} ({{ $period->status }})
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500">Employee (Optional)</label>
        <select name="employee_id" class="border rounded px-2 py-1">
            <option value="">All Employees</option>
            @foreach($employees as $emp)
                <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                    {{ $emp->first_name }} {{ $emp->last_name }}
                </option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="bg-gray-600 text-white px-4 py-1 rounded hover:bg-gray-700">Generate</button>
    @if($selectedPeriod)
    <a href="{{ route('reports.payroll.export', ['period_id' => $selectedPeriod->id]) }}" class="bg-red-600 text-white px-4 py-1 rounded hover:bg-red-700">Export PDF</a>
    @endif
</form>

@if($selectedPeriod)
<div class="bg-white rounded-lg shadow overflow-hidden mb-4">
    <div class="bg-gray-50 px-6 py-3 font-semibold">
        Period: {{ $selectedPeriod->period_name }} - {{ ucfirst($selectedPeriod->status) }}
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Basic Salary</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Allowances</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Deductions</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Net Pay</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($items as $item)
                <tr>
                    <td class="px-4 py-2">{{ $item->employee->first_name }} {{ $item->employee->last_name }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format($item->basic_salary, 2) }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format($item->allowances, 2) }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format($item->deductions, 2) }}</td>
                    <td class="px-4 py-2 text-right font-semibold">{{ number_format($item->net_pay, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-4 text-center text-gray-500">No payroll items found.</td>
                </tr>
            @endforelse
        </tbody>
        @if($items->count() > 0)
        <tfoot class="bg-gray-50 font-semibold">
            <tr>
                <td class="px-4 py-2">TOTALS</td>
                <td class="px-4 py-2 text-right">{{ number_format($items->sum('basic_salary'), 2) }}</td>
                <td class="px-4 py-2 text-right">{{ number_format($items->sum('allowances'), 2) }}</td>
                <td class="px-4 py-2 text-right">{{ number_format($items->sum('deductions'), 2) }}</td>
                <td class="px-4 py-2 text-right">{{ number_format($items->sum('net_pay'), 2) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
@endif
@endsection