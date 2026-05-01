@extends('layouts.app')

@section('title', 'New Fuel Purchase')

@section('header', 'New Fuel Purchase')

@section('content')
<form action="{{ url('fuel/purchases') }}" method="POST" class="max-w-xl bg-white rounded-lg shadow p-6 space-y-6">
    @csrf

    <div>
        <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
        <input type="date" name="date" id="date" value="{{ date('Y-m-d') }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>

    <div>
        <label for="fuel_type" class="block text-sm font-medium text-gray-700">Fuel Type</label>
        <select name="fuel_type" id="fuel_type" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Select</option>
            <option value="diesel">Diesel</option>
            <option value="petrol">Petrol</option>
        </select>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="litres" class="block text-sm font-medium text-gray-700">Litres</label>
            <input type="number" name="litres" id="litres" step="0.01" min="0" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
            <label for="unit_cost" class="block text-sm font-medium text-gray-700">Unit Cost</label>
            <input type="number" name="unit_cost" id="unit_cost" step="0.01" min="0" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
    </div>

    <div>
        <label for="supplier" class="block text-sm font-medium text-gray-700">Supplier</label>
        <input type="text" name="supplier" id="supplier"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>

    <div>
        <label for="receipt_number" class="block text-sm font-medium text-gray-700">Receipt Number</label>
        <input type="text" name="receipt_number" id="receipt_number"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>

    <div class="flex justify-end gap-4">
        <a href="{{ url('fuel/purchases') }}" class="px-4 py-2 text-gray-700 border border-gray-300 rounded hover:bg-gray-50">Cancel</a>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Save Purchase</button>
    </div>
</form>
@endsection