@extends('layouts.app')

@section('title', 'Edit Customer')

@section('header', 'Edit Customer')

@section('content')
<form action="{{ route('customers.update', $customer) }}" method="POST" class="bg-white p-6 rounded-lg shadow max-w-md">
    @csrf
    @method('PUT')

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Customer Name *</label>
        <input type="text" name="name" value="{{ $customer->name }}" required class="mt-1 form-input">
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Phone</label>
        <input type="text" name="phone" value="{{ $customer->phone }}" class="mt-1 form-input">
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Customer Type *</label>
        <select name="type" required class="mt-1 form-select">
            <option value="walk_in" {{ $customer->type == 'walk_in' ? 'selected' : '' }}>Walk-in</option>
            <option value="regular" {{ $customer->type == 'regular' ? 'selected' : '' }}>Regular</option>
            <option value="wholesale" {{ $customer->type == 'wholesale' ? 'selected' : '' }}>Wholesale</option>
        </select>
    </div>

    <div class="mb-4">
        <label class="flex items-center">
            <input type="checkbox" name="is_active" value="1" class="mr-2" {{ $customer->is_active ? 'checked' : '' }}>
            <span class="text-sm">Active</span>
        </label>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('customers.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Cancel</a>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Save</button>
    </div>
</form>
@endsection
