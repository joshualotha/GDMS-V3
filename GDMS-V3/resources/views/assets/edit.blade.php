@extends('layouts.app')

@section('title', 'Edit Asset')

@section('header', 'Edit Asset')

@section('content')
<form action="{{ route('assets.update', $asset) }}" method="POST" class="bg-white p-6 rounded-lg shadow">
    @csrf
    @method('PUT')
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Asset Number</label>
            <input type="text" value="{{ $asset->asset_number }}" readonly class="mt-1 w-full border rounded px-3 py-2 bg-gray-100">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Category *</label>
            <select name="asset_category_id" required class="mt-1 w-full border rounded px-3 py-2">
                <option value="">Select Category</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ $asset->asset_category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Asset Name *</label>
            <input type="text" name="name" value="{{ $asset->name }}" required class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Serial Number</label>
            <input type="text" name="serial_number" value="{{ $asset->serial_number }}" class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Plate Number</label>
            <input type="text" name="plate_number" value="{{ $asset->plate_number }}" class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Purchase Date</label>
            <input type="date" name="purchase_date" value="{{ $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : '' }}" class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Purchase Cost</label>
            <input type="number" name="purchase_cost" step="0.01" value="{{ $asset->purchase_cost }}" class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Annual Depreciation Rate (%)</label>
            <input type="number" name="depreciation_rate" step="0.01" value="{{ $asset->depreciation_rate }}" placeholder="e.g. 20" class="mt-1 w-full border rounded px-3 py-2">
            <p class="text-xs text-gray-500 mt-1">Annual reducing balance rate. The system converts this to monthly automatically.</p>
        </div>

        <div class="bg-gray-50 p-3 rounded border">
            <label class="block text-sm font-medium text-gray-500">Current Book Value (auto-calculated)</label>
            <div class="text-lg font-bold text-gray-800">{{ number_format($asset->current_book_value, 2) }}</div>
        </div>

        <div class="bg-gray-50 p-3 rounded border">
            <label class="block text-sm font-medium text-gray-500">Accumulated Depreciation (auto-calculated)</label>
            <div class="text-lg font-bold text-gray-800">{{ number_format($asset->accumulated_depreciation, 2) }}</div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Assigned to Outlet</label>
            <select name="assigned_to_outlet" class="mt-1 w-full border rounded px-3 py-2">
                <option value="">None</option>
                @foreach($outlets as $outlet)
                <option value="{{ $outlet->id }}" {{ $asset->assigned_to_outlet == $outlet->id ? 'selected' : '' }}>{{ $outlet->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Assigned to Employee</label>
            <select name="assigned_to_employee" class="mt-1 w-full border rounded px-3 py-2">
                <option value="">None</option>
                @foreach($employees as $emp)
                <option value="{{ $emp->id }}" {{ $asset->assigned_to_employee == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status" class="mt-1 w-full border rounded px-3 py-2">
                <option value="active" {{ $asset->status == 'active' ? 'selected' : '' }}>Active</option>
                <option value="disposed" {{ $asset->status == 'disposed' ? 'selected' : '' }}>Disposed</option>
            </select>
        </div>
    </div>

    <div class="mt-6 flex justify-between">
        <a href="{{ route('assets.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Cancel</a>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Update Asset</button>
    </div>
</form>
@endsection