@extends('layouts.app')

@section('title', 'Edit Outlet')

@section('header', 'Edit Outlet')

@section('content')
<form action="{{ route('outlets.update', $outlet) }}" method="POST" class="max-w-xl bg-white rounded-lg shadow p-6 space-y-6">
    @csrf
    @method('PUT')

    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
        <input type="text" name="name" id="name" value="{{ old('name', $outlet->name) }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
        <select name="type" id="type" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="car" {{ $outlet->type == 'car' ? 'selected' : '' }}>Car</option>
            <option value="physical" {{ $outlet->type == 'physical' ? 'selected' : '' }}>Physical Store</option>
        </select>
        @error('type')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
        <input type="text" name="location" id="location" value="{{ old('location', $outlet->location) }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        @error('location')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="plate_number" class="block text-sm font-medium text-gray-700">Plate Number (for cars)</label>
        <input type="text" name="plate_number" id="plate_number" value="{{ old('plate_number', $outlet->plate_number) }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        @error('plate_number')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="is_active" class="inline-flex items-center">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ $outlet->is_active ? 'checked' : '' }}
                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
            <span class="ml-2 text-sm text-gray-700">Active</span>
        </label>
    </div>

    <div class="flex justify-end gap-4">
        <a href="{{ route('outlets.index') }}" class="px-4 py-2 text-gray-700 border border-gray-300 rounded hover:bg-gray-50">
            Cancel
        </a>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            Update
        </button>
    </div>
</form>
@endsection