@extends('layouts.app')

@section('title', 'Create Transfer')

@section('header', 'Create Transfer')

@section('content')
<form action="{{ url('warehouse/transfers') }}" method="POST" class="space-y-6">
    @csrf

    <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label for="outlet_id" class="block text-sm font-medium text-gray-700">Outlet</label>
                <select name="outlet_id" id="outlet_id" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select Outlet</option>
                    @foreach($outlets as $outlet)
                        <option value="{{ $outlet->id }}">{{ $outlet->name }} ({{ $outlet->type }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea name="notes" id="notes" rows="2"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium mb-4">Transfer Items</h3>

        <div id="items-container" class="space-y-4">
            <div class="grid grid-cols-5 gap-2 text-sm font-medium text-gray-500">
                <div>Cylinder Type</div>
                <div>Available Stock</div>
                <div>Quantity</div>
                <div></div>
            </div>

            <div class="item-row grid grid-cols-5 gap-2 items-center">
                <div>
                    <select name="items[0][cylinder_type_id]" required
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 cylinder-select">
                        <option value="">Select</option>
                        @foreach($cylinderTypes as $ct)
                            @php $stock = $stockMain[$ct->id] ?? null; @endphp
                            <option value="{{ $ct->id }}" data-full="{{ $stock->full_qty ?? 0 }}" data-empty="{{ $stock->empty_qty ?? 0 }}">
                                {{ $ct->name }} ({{ $ct->size_kg }}kg)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="text-sm text-gray-600 available-stock">
                    <span class="font-medium">Full: </span><span class="stock-full">0</span> | <span class="font-medium">Empty: </span><span class="stock-empty">0</span>
                </div>
                <div>
                    <input type="number" name="items[0][quantity]" min="1" value="1" required
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <button type="button" class="text-red-600 hover:text-red-800 remove-item">Remove</button>
                </div>
            </div>
        </div>

        <button type="button" id="add-item" class="mt-4 text-indigo-600 hover:text-indigo-800">+ Add Item</button>
    </div>

    <div class="flex justify-end gap-4">
        <a href="{{ url('warehouse/transfers') }}" class="px-4 py-2 text-gray-700 border border-gray-300 rounded hover:bg-gray-50">
            Cancel
        </a>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            Create Transfer
        </button>
    </div>
</form>

<script>
let itemIndex = 1;
document.getElementById('add-item').addEventListener('click', function() {
    const container = document.getElementById('items-container');
    const html = `
        <div class="item-row grid grid-cols-5 gap-2 items-center">
            <div>
                <select name="items[${itemIndex}][cylinder_type_id]" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 cylinder-select">
                    <option value="">Select</option>
                    @foreach($cylinderTypes as $ct)
                        @php $stock = $stockMain[$ct->id] ?? null; @endphp
                        <option value="{{ $ct->id }}" data-full="{{ $stock->full_qty ?? 0 }}" data-empty="{{ $stock->empty_qty ?? 0 }}">
                            {{ $ct->name }} ({{ $ct->size_kg }}kg)
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="text-sm text-gray-600 available-stock">
                <span class="font-medium">Full: </span><span class="stock-full">0</span> | <span class="font-medium">Empty: </span><span class="stock-empty">0</span>
            </div>
            <div>
                <input type="number" name="items[${itemIndex}][quantity]" min="1" value="1" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <button type="button" class="text-red-600 hover:text-red-800 remove-item">Remove</button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    itemIndex++;
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-item')) {
        e.target.closest('.item-row').remove();
    }
});

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('cylinder-select')) {
        const row = e.target.closest('.item-row');
        const selectedOption = e.target.options[e.target.selectedIndex];
        const fullQty = selectedOption.dataset.full || 0;
        const emptyQty = selectedOption.dataset.empty || 0;
        row.querySelector('.stock-full').textContent = fullQty;
        row.querySelector('.stock-empty').textContent = emptyQty;
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const firstSelect = document.querySelector('.cylinder-select');
    if (firstSelect && firstSelect.selectedIndex > 0) {
        const row = firstSelect.closest('.item-row');
        const selectedOption = firstSelect.options[firstSelect.selectedIndex];
        row.querySelector('.stock-full').textContent = selectedOption.dataset.full || 0;
        row.querySelector('.stock-empty').textContent = selectedOption.dataset.empty || 0;
    }
});
</script>
@endsection