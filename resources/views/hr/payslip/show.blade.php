@extends('layouts.app')

@section('title', 'Payslip')

@section('header', 'Payslip: ' . $item->employee->full_name)

@section('content')
<div class="mb-4 flex justify-between items-center">
    <a href="{{ route('payroll.show', $item->period) }}" class="px-4 py-2 border rounded hover:bg-gray-50">Back</a>
    <a href="{{ route('payslip.download', $item) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Download PDF</a>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-2xl mx-auto">
    <div class="text-center border-b pb-4 mb-4">
        <h2 class="text-xl font-bold">PAYSLIP</h2>
        <p class="text-gray-600">{{ $item->period->period_name }}</p>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div>
            <p class="text-sm text-gray-500">Employee Number</p>
            <p class="font-semibold">{{ $item->employee->employee_number }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Employee Name</p>
            <p class="font-semibold">{{ $item->employee->full_name }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Role</p>
            <p class="font-semibold">{{ $item->employee->role_title ?? '-' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Outlet</p>
            <p class="font-semibold">{{ $item->employee->outlet->name ?? 'HQ' }}</p>
        </div>
    </div>

    <table class="w-full">
        <tr class="border-b">
            <td class="py-2 text-gray-500">Basic Salary</td>
            <td class="py-2 text-right">{{ number_format($item->basic_salary, 2) }}</td>
        </tr>
        <tr class="border-b">
            <td class="py-2 text-gray-500">Allowances</td>
            <td class="py-2 text-right">{{ number_format($item->allowances, 2) }}</td>
        </tr>
        @if($item->allowance_note)
        <tr>
            <td class="py-1 text-xs text-gray-500 pl-4">{{ $item->allowance_note }}</td>
        </tr>
        @endif
        <tr class="border-b">
            <td class="py-2 text-gray-500">Deductions</td>
            <td class="py-2 text-right">-{{ number_format($item->deductions, 2) }}</td>
        </tr>
        @if($item->deduction_note)
        <tr>
            <td class="py-1 text-xs text-gray-500 pl-4">{{ $item->deduction_note }}</td>
        </tr>
        @endif
        <tr class="border-b font-bold text-lg">
            <td class="py-3">NET PAY</td>
            <td class="py-3 text-right">{{ number_format($item->net_pay, 2) }}</td>
        </tr>
    </table>

    <div class="mt-6 text-center text-xs text-gray-500">
        <p>Approved: {{ $item->period->status }}</p>
    </div>
</div>
@endsection