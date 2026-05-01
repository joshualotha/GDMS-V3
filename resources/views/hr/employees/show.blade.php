@extends('layouts.app')

@section('title', 'Employee Details')

@section('header', 'Employee: ' . $employee->full_name)

@section('content')
<div class="mb-4 flex justify-end gap-2">
    <a href="{{ route('employees.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Back</a>
    <a href="{{ route('employees.edit', $employee) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Edit</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Employee Details -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Personal Information</h3>
        <dl class="grid grid-cols-2 gap-4">
            <dt class="text-gray-500">Employee Number</dt>
            <dd>{{ $employee->employee_number }}</dd>
            
            <dt class="text-gray-500">Full Name</dt>
            <dd>{{ $employee->full_name }}</dd>
            
            <dt class="text-gray-500">ID Number</dt>
            <dd>{{ $employee->id_number ?? '-' }}</dd>
            
            <dt class="text-gray-500">Phone</dt>
            <dd>{{ $employee->phone ?? '-' }}</dd>
            
            <dt class="text-gray-500">Email</dt>
            <dd>{{ $employee->email ?? '-' }}</dd>
            
            <dt class="text-gray-500">Role / Title</dt>
            <dd>{{ $employee->role_title ?? '-' }}</dd>
            
            <dt class="text-gray-500">Assigned Outlet</dt>
            <dd>{{ $employee->outlet->name ?? 'HQ' }}</dd>
            
            <dt class="text-gray-500">Hire Date</dt>
            <dd>{{ $employee->hire_date ? $employee->hire_date->format('d/m/Y') : '-' }}</dd>
            
            <dt class="text-gray-500">Basic Salary</dt>
            <dd>{{ number_format($employee->basic_salary, 2) }}</dd>
            
            <dt class="text-gray-500">Status</dt>
            <dd>
                <span class="px-2 py-1 text-xs rounded 
                    {{ $employee->status == 'active' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $employee->status == 'inactive' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $employee->status == 'terminated' ? 'bg-red-100 text-red-800' : '' }}">
                    {{ ucfirst($employee->status) }}
                </span>
            </dd>
        </dl>
    </div>

    <!-- Payroll History -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Payroll History</h3>
        @if($employee->payrollItems->count() > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Period</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Basic</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Net Pay</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($employee->payrollItems->sortByDesc('period') as $item)
                        <tr>
                            <td class="px-3 py-2">{{ $item->period->period_name }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($item->basic_salary, 2) }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($item->net_pay, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-gray-500">No payroll history.</p>
        @endif
    </div>
</div>
@endsection