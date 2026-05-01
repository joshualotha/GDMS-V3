@extends('layouts.app')

@section('title', 'Submit Cash')

@section('header', 'Submit Cash for Sale: ' . $sale->sale_number)

@section('content')
<form action="{{ url('sales/' . $sale->id . '/cash') }}" method="POST" class="max-w-xl bg-white rounded-lg shadow p-6 space-y-6" enctype="multipart/form-data">
    @csrf

    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-sm text-gray-500">Expected Amount</p>
            <p class="text-2xl font-bold">{{ number_format($sale->total_price, 2) }}</p>
        </div>
        <div>
            <label for="submitted_amount" class="block text-sm font-medium text-gray-700">Submitted Amount</label>
            <input type="number" name="submitted_amount" id="submitted_amount" step="0.01" min="0" value="{{ $sale->total_price }}" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
    </div>

    <div>
        <label for="receipt_image" class="block text-sm font-medium text-gray-700">Receipt Image (Optional)</label>
        <input type="file" name="receipt_image" id="receipt_image" accept="image/*"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>

    <div>
        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
        <textarea name="notes" id="notes" rows="2"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
    </div>

    <div class="flex justify-end gap-4">
        <a href="{{ url('sales/' . $sale->id) }}" class="px-4 py-2 text-gray-700 border border-gray-300 rounded hover:bg-gray-50">Cancel</a>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Submit Cash</button>
    </div>
</form>
@endsection