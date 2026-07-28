@extends('layouts.app')

@section('title', 'New Fuel Issue')

@section('header', 'New Fuel Issue')

@section('content')
<form action="{{ url('fuel/issues') }}" method="POST" class="max-w-xl bg-white rounded-lg shadow p-6 space-y-6">
    @csrf

    <div>
        <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
        <input type="date" name="date" id="date" value="{{ date('Y-m-d') }}" required
            class="mt-1 form-input">
    </div>

    <div>
        <label for="outlet_id" class="block text-sm font-medium text-gray-700">Vehicle (Car)</label>
        <select name="outlet_id" id="outlet_id" required
            class="mt-1 form-select">
            <option value="">Select Vehicle</option>
            @foreach($outlets as $outlet)
                <option value="{{ $outlet->id }}">{{ $outlet->name }} ({{ $outlet->plate_number ?? 'no plate' }})</option>
            @endforeach
        </select>
        @if($outlets->isEmpty())
            <p class="text-xs text-red-600 mt-1">No car outlets found. <a href="{{ route('outlets.create') }}" class="underline">Add one first</a>.</p>
        @endif
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
            <label for="litres" class="block text-sm font-medium text-gray-700">Litres</label>
            <input type="number" name="litres" id="litres" step="0.01" min="0.01" required
                class="mt-1 form-input">
        </div>
        <div>
            <label for="odometer_km" class="block text-sm font-medium text-gray-700">Odometer (km)</label>
            <input type="number" name="odometer_km" id="odometer_km"
                class="mt-1 form-input">
        </div>
    </div>

    <div>
        <label for="issued_by" class="block text-sm font-medium text-gray-700">Issued By</label>
        <input type="text" name="issued_by" id="issued_by"
            class="mt-1 form-input">
    </div>

    <div class="flex justify-end gap-4">
        <a href="{{ url('fuel/issues') }}" class="px-4 py-2 text-gray-700 border border-gray-300 rounded hover:bg-gray-50">Cancel</a>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Issue Fuel</button>
    </div>
</form>
@endsection