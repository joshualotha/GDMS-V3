@extends('layouts.app')

@section('title', 'New Fuel Purchase')

@section('header', 'New Fuel Purchase')

@section('content')
<form action="{{ url('fuel/purchases') }}" method="POST" class="max-w-xl bg-white rounded-lg shadow p-6 space-y-6">
    @csrf

    <div>
        <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
        <input type="date" name="date" id="date" value="{{ date('Y-m-d') }}" required
            class="mt-1 form-input">
    </div>

    <div>
        <label for="outlet_id" class="block text-sm font-medium text-gray-700">Vehicle</label>
        <select name="outlet_id" id="outlet_id" required
            class="mt-1 form-select">
            <option value="">Select vehicle</option>
            @foreach($vehicles as $vehicle)
                <option value="{{ $vehicle->id }}">{{ $vehicle->name }}{{ $vehicle->plate_number ? ' ('.$vehicle->plate_number.')' : '' }}</option>
            @endforeach
        </select>
        @if($vehicles->isEmpty())
            <p class="text-xs text-red-600 mt-1">No active car outlets exist yet. <a href="{{ route('outlets.create') }}" class="underline">Add one first</a>.</p>
        @endif
    </div>

    <div>
        <label for="odometer_km" class="block text-sm font-medium text-gray-700">Odometer Reading (km)</label>
        <input type="number" name="odometer_km" id="odometer_km" min="0" required
            class="mt-1 form-input">
    </div>

    <div>
        <label for="fuel_type" class="block text-sm font-medium text-gray-700">Fuel Type</label>
        <select name="fuel_type" id="fuel_type" required
            class="mt-1 form-select">
            <option value="">Select</option>
            <option value="diesel">Diesel</option>
            <option value="petrol">Petrol</option>
        </select>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="litres" class="block text-sm font-medium text-gray-700">Litres Received</label>
            <input type="number" name="litres" id="litres" step="0.01" min="0" required
                class="mt-1 form-input">
        </div>
        <div>
            <label for="total_cost" class="block text-sm font-medium text-gray-700">Amount Paid (Cash)</label>
            <input type="number" name="total_cost" id="total_cost" step="0.01" min="0" required
                class="mt-1 form-input">
        </div>
    </div>

    <div>
        <label for="supplier_id" class="block text-sm font-medium text-gray-700">Petrol Station (optional)</label>
        <select name="supplier_id" id="supplier_id"
            class="mt-1 form-select">
            <option value="">Select Supplier</option>
            @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="receipt_number" class="block text-sm font-medium text-gray-700">Receipt Number</label>
        <input type="text" name="receipt_number" id="receipt_number"
            class="mt-1 form-input">
    </div>

    <div class="flex justify-end gap-4">
        <a href="{{ url('fuel/purchases') }}" class="px-4 py-2 text-gray-700 border border-gray-300 rounded hover:bg-gray-50">Cancel</a>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Save Purchase</button>
    </div>
</form>
@endsection
