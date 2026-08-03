@extends('layouts.app')

@section('title', 'Edit Expense')

@section('header', 'Edit Expense')

@section('content')
<form action="{{ route('expenses.update', $expense) }}" method="POST" class="bg-white p-6 rounded-lg shadow max-w-lg">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Category *</label>
            <select name="expense_category_id" required class="mt-1 form-select">
                <option value="">Select Category</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('expense_category_id', $expense->expense_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Date *</label>
            <input type="date" name="expense_date" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required class="mt-1 form-input">
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Description *</label>
            <textarea name="description" required rows="2" class="mt-1 form-input">{{ old('description', $expense->description) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Amount *</label>
            <input type="number" name="amount" step="0.01" value="{{ old('amount', $expense->amount) }}" required class="mt-1 form-input">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Reference</label>
            <input type="text" name="reference" value="{{ old('reference', $expense->reference) }}" class="mt-1 form-input" placeholder="Invoice #, Receipt #, etc.">
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Related Asset</label>
            <select name="asset_id" class="mt-1 form-select">
                <option value="">-- None --</option>
                @foreach($assets as $asset)
                    <option value="{{ $asset->id }}" {{ (string) old('asset_id', $expense->asset_id) === (string) $asset->id ? 'selected' : '' }}>{{ $asset->name }}{{ $asset->plate_number ? ' ('.$asset->plate_number.')' : '' }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="mt-6 flex justify-end gap-3">
        <a href="{{ route('expenses.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Cancel</a>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Save</button>
    </div>
</form>
@endsection
