@extends('layouts.app')

@section('title', 'Add Outlet')

@section('header', 'Add Outlet')

@section('content')
<div class="max-w-3xl mx-auto">
    <form action="{{ route('outlets.store') }}" method="POST" class="bg-white rounded-lg shadow-lg p-8">
        @csrf

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" required 
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
            <select name="type" id="outlet-type" required 
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                onchange="toggleCarFields(this.value)">
                <option value="">Select Type</option>
                <option value="car" {{ old('type') == 'car' ? 'selected' : '' }}>Car (Vehicle)</option>
                <option value="physical" {{ old('type') == 'physical' ? 'selected' : '' }}>Physical Store</option>
            </select>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Assigned Employee *</label>
            <select name="employee_id" id="employee-select" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                onchange="toggleNewEmployeeFields()">
                <option value="">Select employee</option>
                @foreach($availableEmployees as $emp)
                <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }} ({{ $emp->employee_number }})</option>
                @endforeach
                <option value="__new__" {{ old('employee_id') == '__new__' ? 'selected' : '' }}>+ Add New Employee</option>
            </select>
            <p class="text-xs text-gray-500 mt-1">Every outlet needs exactly one employee. For a car outlet, this person is also the vehicle's driver.</p>
        </div>

        <div id="new-employee-fields" class="{{ old('employee_id') == '__new__' ? '' : 'hidden' }} bg-gray-50 rounded-lg p-4 mb-6">
            <h4 class="font-medium text-gray-900 mb-3">New Employee Details</h4>
            <div class="grid-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                    <input type="text" name="new_employee_first_name" value="{{ old('new_employee_first_name') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                    <input type="text" name="new_employee_last_name" value="{{ old('new_employee_last_name') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div class="grid-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="text" name="new_employee_phone" value="{{ old('new_employee_phone') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role / Title</label>
                    <input type="text" name="new_employee_role_title" value="{{ old('new_employee_role_title') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div class="grid-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hire Date</label>
                    <input type="date" name="new_employee_hire_date" value="{{ old('new_employee_hire_date', date('Y-m-d')) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pay Type</label>
                    <select name="new_employee_pay_type" id="new-employee-pay-type"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                        onchange="toggleNewEmployeePayFields()">
                        <option value="salary" {{ old('new_employee_pay_type') == 'salary' ? 'selected' : '' }}>Salary</option>
                        <option value="commission" {{ old('new_employee_pay_type') == 'commission' ? 'selected' : '' }}>Commission</option>
                    </select>
                </div>
            </div>
            <div class="grid-2 gap-4 mt-4">
                <div id="new-employee-salary-field">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Basic Salary</label>
                    <input type="number" name="new_employee_basic_salary" value="{{ old('new_employee_basic_salary') }}" step="0.01" min="0"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div id="new-employee-commission-fields" class="hidden grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Commission Rate</label>
                        <input type="number" name="new_employee_commission_rate" value="{{ old('new_employee_commission_rate') }}" step="0.01" min="0"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Commission Target</label>
                        <input type="number" name="new_employee_commission_target" value="{{ old('new_employee_commission_target', 1250) }}" min="1"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>
        </div>

        <div id="car-fields" class="{{ old('type') != 'car' ? 'hidden' : '' }}">
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <h4 class="font-medium text-blue-900 mb-3">Vehicle Details</h4>
                <p class="text-xs text-gray-600 mb-4">A car outlet is a depreciable asset. Either link it to an asset you've already added, or fill in the details below to create it now.</p>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Link to Existing Asset</label>
                    <select name="asset_id" id="asset-select" onchange="toggleNewAssetFields()"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Create a new asset for this vehicle --</option>
                        @foreach($availableAssets as $asset)
                        <option value="{{ $asset->id }}">{{ $asset->name }} ({{ $asset->plate_number ?? 'no plate' }})</option>
                        @endforeach
                    </select>
                </div>

                <div id="new-asset-fields">
                    <div class="grid-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Plate Number</label>
                            <input type="text" name="plate_number" value="{{ old('plate_number') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                placeholder="e.g. T 123 ABC">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Cost</label>
                            <input type="number" name="purchase_cost" value="{{ old('purchase_cost') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                placeholder="0">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Depreciation Rate (% / year)</label>
                        <input type="number" name="depreciation_rate" id="depreciation-rate" value="{{ old('depreciation_rate', 20) }}" step="0.01" min="0" max="100"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <p class="text-xs text-gray-500 mt-1">Annual reducing-balance rate for this specific vehicle. Defaults to 20% &mdash; adjust as needed.</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="physical-fields" class="{{ old('type') == 'car' ? 'hidden' : '' }}">
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Location *</label>
                <input type="text" name="location" id="location-input" value="{{ old('location') }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Address or description">
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Date Opened *</label>
            <input type="date" name="opened_date" value="{{ old('opened_date', date('Y-m-d')) }}" required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div class="mb-6">
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                    class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-700">Active</span>
            </label>
        </div>

        <div class="flex justify-between pt-4 border-t">
            <a href="{{ route('outlets.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                Save Outlet
            </button>
        </div>
    </form>
</div>

<script>
function toggleCarFields(type) {
    var locationInput = document.getElementById('location-input');
    if (type === 'car') {
        document.getElementById('car-fields').classList.remove('hidden');
        document.getElementById('physical-fields').classList.add('hidden');
        locationInput.required = false;
        toggleNewAssetFields();
    } else {
        document.getElementById('car-fields').classList.add('hidden');
        document.getElementById('physical-fields').classList.remove('hidden');
        locationInput.required = true;
        document.getElementById('depreciation-rate').required = false;
    }
}
document.addEventListener('DOMContentLoaded', function () {
    toggleCarFields(document.getElementById('outlet-type').value);
});

function toggleNewAssetFields() {
    var linkingToExisting = document.getElementById('asset-select').value !== '';
    var fields = document.getElementById('new-asset-fields');
    var rateInput = document.getElementById('depreciation-rate');
    fields.classList.toggle('hidden', linkingToExisting);
    rateInput.required = !linkingToExisting;
}

function toggleNewEmployeeFields() {
    var isNew = document.getElementById('employee-select').value === '__new__';
    document.getElementById('new-employee-fields').classList.toggle('hidden', !isNew);
    if (isNew) {
        toggleNewEmployeePayFields();
    }
}

function toggleNewEmployeePayFields() {
    var isCommission = document.getElementById('new-employee-pay-type').value === 'commission';
    document.getElementById('new-employee-salary-field').classList.toggle('hidden', isCommission);
    document.getElementById('new-employee-commission-fields').classList.toggle('hidden', !isCommission);
}
document.addEventListener('DOMContentLoaded', function () {
    toggleNewEmployeeFields();
});
</script>
@endsection