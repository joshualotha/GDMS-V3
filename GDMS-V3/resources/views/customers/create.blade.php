@extends('layouts.app')

@section('title', 'Add Customer')

@section('header', 'Add Customer')

@section('content')
<form action="{{ route('customers.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow max-w-md">
    @csrf
    
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Customer Name *</label>
        <input type="text" name="name" required class="mt-1 w-full border rounded px-3 py-2">
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Phone</label>
        <input type="text" name="phone" class="mt-1 w-full border rounded px-3 py-2">
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Customer Type *</label>
        <select name="type" required class="mt-1 w-full border rounded px-3 py-2">
            <option value="walk_in">Walk-in</option>
            <option value="regular">Regular</option>
            <option value="wholesale">Wholesale</option>
        </select>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('customers.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Cancel</a>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Save</button>
    </div>
</form>
@endsection