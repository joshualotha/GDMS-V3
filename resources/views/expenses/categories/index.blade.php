@extends('layouts.app')

@section('title', 'Expense Categories')

@section('header', 'Expense Categories')

@section('content')
<div class="mb-4 flex justify-between items-center">
    <p class="text-sm text-gray-600">Manage expense categories for tracking business expenses.</p>
    <button onclick="document.getElementById('createModal').showModal()" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Add Category</button>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($categories as $category)
                <tr>
                    <td class="px-6 py-4">{{ $category->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $category->description ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <button onclick="document.getElementById('editModal{{ $category->id }}').showModal()" class="text-indigo-600 hover:underline mr-3">Edit</button>
                        <form action="{{ route('expense-categories.toggle', $category) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Toggle active status?')">
                                {{ $category->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No expense categories found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Create Modal -->
<dialog id="createModal" class="modal p-6 bg-white rounded-lg shadow-xl">
    <form action="{{ route('expense-categories.store') }}" method="POST" class="w-96">
        @csrf
        <h3 class="text-lg font-semibold mb-4">Add Expense Category</h3>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Name *</label>
            <input type="text" name="name" required class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" class="mt-1 w-full border rounded px-3 py-2"></textarea>
        </div>

        <div class="flex justify-end gap-2">
            <button type="button" onclick="document.getElementById('createModal').close()" class="px-4 py-2 border rounded">Cancel</button>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Save</button>
        </div>
    </form>
</dialog>

<!-- Edit Modals -->
@foreach($categories as $category)
<dialog id="editModal{{ $category->id }}" class="modal p-6 bg-white rounded-lg shadow-xl">
    <form action="{{ route('expense-categories.update', $category) }}" method="POST" class="w-96">
        @csrf @method('PUT')
        <h3 class="text-lg font-semibold mb-4">Edit Expense Category</h3>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Name *</label>
            <input type="text" name="name" value="{{ $category->name }}" required class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" class="mt-1 w-full border rounded px-3 py-2">{{ $category->description }}</textarea>
        </div>

        <div class="flex justify-end gap-2">
            <button type="button" onclick="document.getElementById('editModal{{ $category->id }}').close()" class="px-4 py-2 border rounded">Cancel</button>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Update</button>
        </div>
    </form>
</dialog>
@endforeach
@endsection