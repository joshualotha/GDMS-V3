@extends('layouts.app')

@section('title', 'Expenses')

@section('header', 'Expenses')

@section('content')
<div class="mb-4 flex justify-between items-center">
    <div class="flex gap-2">
        <a href="{{ route('expense-categories.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Categories</a>
    </div>
    <a href="{{ route('expenses.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
        Add Expense
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<form method="GET" class="bg-white p-4 rounded-lg shadow mb-4 flex flex-wrap gap-4 items-end">
    <div>
        <label class="block text-xs text-gray-500">Category</label>
        <select name="category_id" class="border rounded px-2 py-1">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500">Date From</label>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="border rounded px-2 py-1">
    </div>
    <div>
        <label class="block text-xs text-gray-500">Date To</label>
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="border rounded px-2 py-1">
    </div>
    <button type="submit" class="bg-gray-600 text-white px-4 py-1 rounded hover:bg-gray-700">Filter</button>
    <a href="{{ route('expenses.index') }}" class="px-4 py-1 text-gray-600 hover:underline">Clear</a>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expense #</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($expenses as $expense)
                <tr>
                    <td class="px-6 py-4">{{ $expense->expense_number }}</td>
                    <td class="px-6 py-4">{{ $expense->expense_date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">{{ $expense->category->name }}</td>
                    <td class="px-6 py-4">{{ $expense->description }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($expense->amount, 2) }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $expense->reference ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No expenses found.</td>
                </tr>
            @endforelse
        </tbody>
        @if($expenses->count() > 0)
        <tfoot class="bg-gray-50 font-semibold">
            <tr>
                <td colspan="4" class="px-6 py-3 text-right">Total</td>
                <td class="px-6 py-3 text-right">{{ number_format($expenses->sum('amount'), 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
@endsection