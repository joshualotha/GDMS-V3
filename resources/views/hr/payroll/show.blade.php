@extends('layouts.app')

@section('title', 'Payroll: ' . $period->period_name)

@section('header', 'Payroll: ' . $period->period_name)

@section('content')
<div class="mb-4 flex justify-between items-center">
    <div class="flex gap-2">
        <a href="{{ route('payroll.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Back</a>
        @if($period->status == 'draft')
            <form action="{{ route('payroll.approve', $period) }}" method="POST">
                @csrf
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Approve</button>
            </form>
        @elseif($period->status == 'approved')
            <form action="{{ route('payroll.markPaid', $period) }}" method="POST">
                @csrf
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Mark as Paid</button>
            </form>
        @endif
    </div>
    <div class="text-right">
        <span class="px-2 py-1 text-xs rounded 
            {{ $period->status == 'draft' ? 'bg-yellow-100 text-yellow-800' : '' }}
            {{ $period->status == 'approved' ? 'bg-blue-100 text-blue-800' : '' }}
            {{ $period->status == 'paid' ? 'bg-green-100 text-green-800' : '' }}">
            {{ ucfirst($period->status) }}
        </span>
    </div>
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

@if($period->status == 'draft')
    <p class="text-sm text-gray-600 mb-4">Edit allowances and deductions below, then click Save. Changes will update automatically.</p>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden mb-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Basic</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Allowances</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Allowance Note</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Deductions</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deduction Note</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Net Pay</th>
                @if($period->status == 'draft')
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                @endif
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($period->items as $item)
                <tr>
                    <td class="px-4 py-4">
                        {{ $item->employee->full_name }}<br>
                        <span class="text-xs text-gray-500">{{ $item->employee->employee_number }}</span>
                        @if($period->status != 'draft')
                        <br><a href="{{ route('payslip.show', $item) }}" class="text-xs text-indigo-600 hover:underline">Payslip</a>
                        @endif
                    </td>
                    <td class="px-4 py-4 text-right">{{ number_format($item->basic_salary, 2) }}</td>
                    @if($period->status == 'draft')
                    <form action="{{ route('payroll.item.update', $item) }}" method="POST" class="flex gap-1">
                        @csrf
                        @method('PUT')
                        <td class="px-2 py-2"><input type="number" name="allowances" step="0.01" value="{{ $item->allowances }}" class="w-24 border rounded px-2 py-1 text-right"></td>
                        <td class="px-2 py-2"><input type="text" name="allowance_note" value="{{ $item->allowance_note }}" placeholder="Note" class="w-28 border rounded px-2 py-1"></td>
                        <td class="px-2 py-2"><input type="number" name="deductions" step="0.01" value="{{ $item->deductions }}" class="w-24 border rounded px-2 py-1 text-right"></td>
                        <td class="px-2 py-2"><input type="text" name="deduction_note" value="{{ $item->deduction_note }}" placeholder="Note" class="w-28 border rounded px-2 py-1"></td>
                        <td class="px-4 py-4 text-right font-semibold">{{ number_format($item->net_pay, 2) }}</td>
                        <td class="px-2 py-2"><button type="submit" class="text-indigo-600 hover:underline">Save</button></td>
                    </form>
                    @else
                    <td class="px-4 py-4 text-right">{{ number_format($item->allowances, 2) }}</td>
                    <td class="px-4 py-4 text-xs">{{ $item->allowance_note ?? '-' }}</td>
                    <td class="px-4 py-4 text-right">{{ number_format($item->deductions, 2) }}</td>
                    <td class="px-4 py-4 text-xs">{{ $item->deduction_note ?? '-' }}</td>
                    <td class="px-4 py-4 text-right font-semibold">{{ number_format($item->net_pay, 2) }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $period->status == 'draft' ? 8 : 7 }}" class="px-6 py-4 text-center text-gray-500">No employees in this period.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot class="bg-gray-50 font-semibold">
            <tr>
                <td class="px-4 py-3">Totals</td>
                <td class="px-4 py-3 text-right">{{ number_format($period->items->sum('basic_salary'), 2) }}</td>
                <td class="px-4 py-3 text-right">{{ number_format($period->items->sum('allowances'), 2) }}</td>
                <td class="px-4 py-3"></td>
                <td class="px-4 py-3 text-right">{{ number_format($period->items->sum('deductions'), 2) }}</td>
                <td class="px-4 py-3"></td>
                <td class="px-4 py-3 text-right">{{ number_format($period->items->sum('net_pay'), 2) }}</td>
                @if($period->status == 'draft')
                <td></td>
                @endif
            </tr>
        </tfoot>
    </table>
</div>
@endsection