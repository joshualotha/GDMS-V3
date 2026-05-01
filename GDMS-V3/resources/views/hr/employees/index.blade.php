@extends('layouts.app')

@section('title', 'Employees')

@section('header', 'Employees')

@section('content')
<div class="mb-4 flex justify-end">
    <a href="{{ route('employees.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
        Add Employee
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Emp #</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Outlet</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Salary</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($employees as $emp)
                <tr>
                    <td class="px-6 py-4">{{ $emp->employee_number }}</td>
                    <td class="px-6 py-4">{{ $emp->full_name }}</td>
                    <td class="px-6 py-4">{{ $emp->role_title ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $emp->outlet->name ?? 'HQ' }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($emp->basic_salary, 2) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded 
                            {{ $emp->status == 'active' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $emp->status == 'inactive' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $emp->status == 'terminated' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($emp->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('employees.show', $emp) }}" class="text-indigo-600 hover:underline mr-3">View</a>
                        <a href="{{ route('employees.edit', $emp) }}" class="text-indigo-600 hover:underline mr-3">Edit</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No employees found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection