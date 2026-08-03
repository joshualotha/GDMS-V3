@extends('layouts.app')

@section('title', 'Add Employee')

@section('header', 'Add Employee')

@section('content')
<form action="{{ route('employees.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow">
    @csrf
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">First Name *</label>
            <input type="text" name="first_name" required class="mt-1 form-input">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Last Name *</label>
            <input type="text" name="last_name" required class="mt-1 form-input">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">ID Number</label>
            <input type="text" name="id_number" class="mt-1 form-input">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Phone</label>
            <input type="text" name="phone" class="mt-1 form-input">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" class="mt-1 form-input">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Role / Title</label>
            <input type="text" name="role_title" class="mt-1 form-input">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Assigned Outlet</label>
            <select name="outlet_id" class="mt-1 form-select">
                <option value="">HQ / None</option>
                @foreach($outlets as $outlet)
                    <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Hire Date</label>
            <input type="date" name="hire_date" class="mt-1 form-input">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Pay Type *</label>
            <select name="pay_type" id="pay_type" required class="mt-1 form-select" onchange="togglePayFields()">
                <option value="salary">Salary</option>
                <option value="commission">Commission (per cylinder sold)</option>
            </select>
        </div>

        <div></div>

        <div id="salary-field">
            <label class="block text-sm font-medium text-gray-700">Basic Salary *</label>
            <input type="number" name="basic_salary" id="basic_salary" step="0.01" class="mt-1 form-input">
        </div>

        <div id="commission-fields" class="hidden contents">
            <div>
                <label class="block text-sm font-medium text-gray-700">Price per Cylinder *</label>
                <input type="number" name="commission_rate" id="commission_rate" step="0.01" class="mt-1 form-input">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Monthly Target (cylinders)</label>
                <input type="number" name="commission_target" id="commission_target" value="1250" min="1" class="mt-1 form-input">
                <p class="text-xs text-gray-500 mt-1">At/above target: rate &times; cylinders sold. Below target: rate scales down by how far short they fell.</p>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status" class="mt-1 form-select">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="terminated">Terminated</option>
            </select>
        </div>
    </div>

    <div class="mt-6 flex justify-end gap-3">
        <a href="{{ route('employees.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Cancel</a>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Save Employee</button>
    </div>
</form>

<script>
function togglePayFields() {
    var isCommission = document.getElementById('pay_type').value === 'commission';

    document.getElementById('salary-field').classList.toggle('hidden', isCommission);
    document.getElementById('commission-fields').classList.toggle('hidden', !isCommission);

    var basicSalary = document.getElementById('basic_salary');
    var commissionRate = document.getElementById('commission_rate');

    basicSalary.disabled = isCommission;
    basicSalary.required = !isCommission;
    commissionRate.disabled = !isCommission;
    commissionRate.required = isCommission;
}
document.addEventListener('DOMContentLoaded', togglePayFields);
</script>
@endsection