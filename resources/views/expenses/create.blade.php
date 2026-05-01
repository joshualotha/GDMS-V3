@extends('layouts.app')

@section('title', 'Add Expense')

@section('header', 'Add Expense')

@section('content')
@if($categoryWarning)
    <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded">
        {{ $categoryWarning }}
        <a href="{{ route('expense-categories.create') }}" class="underline font-medium">Create Category</a>
    </div>
@endif

<form action="{{ route('expenses.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow max-w-lg">
    @csrf
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Category *</label>
            <select name="expense_category_id" required class="mt-1 w-full border rounded px-3 py-2">
                <option value="">Select Category</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Date *</label>
            <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" required class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Description *</label>
            <textarea name="description" required rows="2" class="mt-1 w-full border rounded px-3 py-2"></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Amount *</label>
            <input type="number" name="amount" step="0.01" required class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Reference</label>
            <input type="text" name="reference" class="mt-1 w-full border rounded px-3 py-2" placeholder="Invoice #, Receipt #, etc.">
        </div>
    </div>

    <div class="mt-6 flex justify-end gap-3">
        <a href="{{ route('expenses.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Cancel</a>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Save</button>
    </div>
</form>
@endsection