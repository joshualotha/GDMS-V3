@extends('layouts.app')

@section('title', 'Employee Details')

@section('header', 'Employee: ' . $employee->full_name)

@section('content')
<div class="mb-4 flex justify-end gap-2">
    <a href="{{ route('employees.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Back</a>
    <a href="{{ route('employees.edit', $employee) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Edit</a>
    <form action="{{ route('employees.destroy', $employee) }}" method="POST" onsubmit="return confirm('Delete this employee permanently? This cannot be undone.')">
        @csrf
        @method('DELETE')
        <button type="submit" class="bg-gray-200 text-red-700 px-4 py-2 rounded hover:bg-gray-300">Delete</button>
    </form>
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
            
            <dt class="text-gray-500">Pay Type</dt>
            <dd>{{ $employee->pay_type == 'commission' ? 'Commission' : 'Salary' }}</dd>

            @if($employee->pay_type == 'commission')
                <dt class="text-gray-500">Price per Cylinder</dt>
                <dd>{{ number_format($employee->commission_rate, 2) }}</dd>

                <dt class="text-gray-500">Monthly Target</dt>
                <dd>{{ $employee->commission_target }} cylinders</dd>
            @else
                <dt class="text-gray-500">Basic Salary</dt>
                <dd>{{ number_format($employee->basic_salary, 2) }}</dd>
            @endif

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
                    @foreach($employee->payrollItems->sortByDesc(fn($item) => $item->period->period_year * 100 + $item->period->period_month) as $item)
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

    <!-- Assigned Assets -->
    <div class="bg-white rounded-lg shadow p-6 md:col-span-2">
        <h3 class="text-lg font-semibold mb-4">Assigned Assets</h3>
        @if($employee->assets->count() > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Asset #</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Name</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Category</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Plate Number</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($employee->assets as $asset)
                        <tr>
                            <td class="px-3 py-2"><a href="{{ route('assets.show', $asset) }}" class="text-indigo-600 hover:underline">{{ $asset->asset_number }}</a></td>
                            <td class="px-3 py-2">{{ $asset->name }}</td>
                            <td class="px-3 py-2">{{ $asset->category->name ?? 'N/A' }}</td>
                            <td class="px-3 py-2">{{ $asset->plate_number ?? '-' }}</td>
                            <td class="px-3 py-2">
                                <span class="px-2 py-1 text-xs rounded {{ $asset->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucwords(str_replace('_', ' ', $asset->status)) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-gray-500">No assets assigned to this employee.</p>
        @endif
    </div>
</div>
@endsection