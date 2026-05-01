@extends('layouts.app')

@section('title', 'Add New Asset')

@section('header', 'Add New Asset')

@section('content')
@if($categoryWarning)
    <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded">
        {{ $categoryWarning }}
        <a href="{{ route('asset-categories.create') }}" class="underline font-medium">Create Category</a>
    </div>
@endif
<form action="{{ route('assets.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow">
    @csrf
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Asset Name *</label>
            <input type="text" name="name" required class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Category *</label>
            <select name="asset_category_id" required class="mt-1 w-full border rounded px-3 py-2">
                <option value="">Select Category</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Serial Number</label>
            <input type="text" name="serial_number" class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Plate Number</label>
            <input type="text" name="plate_number" class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Purchase Date</label>
            <input type="date" name="purchase_date" class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Purchase Cost *</label>
            <input type="number" name="purchase_cost" step="0.01" required class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Annual Depreciation Rate (%)</label>
            <input type="number" name="depreciation_rate" step="0.01" placeholder="e.g. 20" class="mt-1 w-full border rounded px-3 py-2">
            <p class="text-xs text-gray-500 mt-1">Annual reducing balance rate. The system converts this to monthly automatically.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Assigned to Outlet</label>
            <select name="assigned_to_outlet" class="mt-1 w-full border rounded px-3 py-2">
                <option value="">None</option>
                @foreach($outlets as $outlet)
                    <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Assigned to Employee</label>
            <select name="assigned_to_employee" class="mt-1 w-full border rounded px-3 py-2">
                <option value="">None</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="mt-6 flex justify-end gap-3">
        <a href="{{ route('assets.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Cancel</a>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Save Asset</button>
    </div>
</form>
@endsection