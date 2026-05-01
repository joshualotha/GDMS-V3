@extends('layouts.app')

@section('title', 'Edit Employee')

@section('header', 'Edit Employee')

@section('content')
<form action="{{ route('employees.update', $employee) }}" method="POST" class="bg-white p-6 rounded-lg shadow">
    @csrf
    @method('PUT')
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">First Name *</label>
            <input type="text" name="first_name" value="{{ $employee->first_name }}" required class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Last Name *</label>
            <input type="text" name="last_name" value="{{ $employee->last_name }}" required class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">ID Number</label>
            <input type="text" name="id_number" value="{{ $employee->id_number }}" class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Phone</label>
            <input type="text" name="phone" value="{{ $employee->phone }}" class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" value="{{ $employee->email }}" class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Role / Title</label>
            <input type="text" name="role_title" value="{{ $employee->role_title }}" class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Assigned Outlet</label>
            <select name="outlet_id" class="mt-1 w-full border rounded px-3 py-2">
                <option value="">HQ / None</option>
                @foreach($outlets as $outlet)
                    <option value="{{ $outlet->id }}" {{ $employee->outlet_id == $outlet->id ? 'selected' : '' }}>{{ $outlet->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Hire Date</label>
            <input type="date" name="hire_date" value="{{ $employee->hire_date }}" class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Basic Salary *</label>
            <input type="number" name="basic_salary" step="0.01" value="{{ $employee->basic_salary }}" required class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status" class="mt-1 w-full border rounded px-3 py-2">
                <option value="active" {{ $employee->status == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $employee->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="terminated" {{ $employee->status == 'terminated' ? 'selected' : '' }}>Terminated</option>
            </select>
        </div>
    </div>

    <div class="mt-6 flex justify-end gap-3">
        <a href="{{ route('employees.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Cancel</a>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Update Employee</button>
    </div>
</form>
@endsection