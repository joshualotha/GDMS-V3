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
            <select name="employee_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                <option value="">Select employee</option>
                @foreach($availableEmployees as $emp)
                <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }} ({{ $emp->employee_number }})</option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1">Every outlet needs exactly one employee. For a car outlet, this person is also the vehicle's driver.</p>
            @if($availableEmployees->isEmpty())
                <p class="text-xs text-red-600 mt-1">No unassigned active employees available. <a href="{{ route('employees.create') }}" class="underline">Add one first</a>.</p>
            @endif
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
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Asset Category *</label>
                        <select name="asset_category_id" id="asset-category"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            <option value="">Select category</option>
                            @foreach($depreciableCategories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->default_depreciation_rate }}% / yr)</option>
                            @endforeach
                        </select>
                        @if($depreciableCategories->isEmpty())
                            <p class="text-xs text-red-600 mt-1">No depreciable asset categories exist yet. <a href="{{ route('asset-categories.index') }}" class="underline">Create one first</a>.</p>
                        @endif
                    </div>
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
        document.getElementById('asset-category').required = false;
    }
}
document.addEventListener('DOMContentLoaded', function () {
    toggleCarFields(document.getElementById('outlet-type').value);
});

function toggleNewAssetFields() {
    var linkingToExisting = document.getElementById('asset-select').value !== '';
    var fields = document.getElementById('new-asset-fields');
    var categorySelect = document.getElementById('asset-category');
    fields.classList.toggle('hidden', linkingToExisting);
    categorySelect.required = !linkingToExisting;
}
</script>
@endsection