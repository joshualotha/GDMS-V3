@extends('layouts.app')

@section('title', 'Empty Return')

@section('header', 'Empty Return')

@section('content')
<form action="{{ url('warehouse/empty-returns') }}" method="POST" class="space-y-6">
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
        <h3 class="text-lg font-medium mb-4">Return Items</h3>
        <p class="text-sm text-gray-500 mb-4">Select an outlet above first to see available empty stock.</p>

        <div id="items-container" class="space-y-4">
            <div class="grid grid-cols-4 gap-2 text-sm font-medium text-gray-500">
                <div>Cylinder Type</div>
                <div>Available Empty</div>
                <div>Quantity (Empty)</div>
                <div></div>
            </div>

            <div class="item-row grid grid-cols-4 gap-2 items-center">
                <div>
                    <select name="items[0][cylinder_type_id]" required disabled
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 cylinder-select">
                        <option value="">Select</option>
                    </select>
                </div>
                <div class="text-sm text-gray-600 empty-stock">
                    <span class="stock-empty">-</span>
                </div>
                <div>
                    <input type="number" name="items[0][quantity]" min="1" value="1" required disabled
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
        <a href="{{ url('warehouse/empty-returns') }}" class="px-4 py-2 text-gray-700 border border-gray-300 rounded hover:bg-gray-50">
            Cancel
        </a>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            Submit Return
        </button>
    </div>
</form>

<script>
let itemIndex = 1;
let currentOutletId = null;
let stockData = {};
const cylinderTypes = @json($cylinderTypes);

document.getElementById('outlet_id').addEventListener('change', function() {
    const outletId = this.value;
    if (!outletId) {
        currentOutletId = null;
        stockData = {};
        disableItems(true);
        return;
    }
    currentOutletId = outletId;
    
    fetch('{{ url("warehouse/empty-returns/stock") }}/' + outletId)
        .then(res => res.json())
        .then(data => {
            stockData = data;
            enableItems();
        })
        .catch(err => {
            console.error('Error loading stock:', err);
            alert('Error loading stock data');
        });
});

function disableItems(disabled) {
    document.querySelectorAll('.cylinder-select').forEach(select => {
        select.disabled = disabled;
        select.innerHTML = '<option value="">Select outlet first</option>';
    });
    document.querySelectorAll('.empty-stock').forEach(el => {
        el.querySelector('.stock-empty').textContent = '-';
    });
    document.querySelectorAll('input[name^="items"]').forEach(input => {
        input.disabled = disabled;
    });
}

function enableItems() {
    const options = cylinderTypes.map(ct => {
        const stock = stockData[ct.id] || { empty_qty: 0 };
        return '<option value="' + ct.id + '" data-empty="' + stock.empty_qty + '">' + ct.name + ' (' + ct.size_kg + 'kg)</option>';
    }).join('');
    
    document.querySelectorAll('.cylinder-select').forEach(select => {
        select.disabled = false;
        select.innerHTML = '<option value="">Select</option>' + options;
    });
    document.querySelectorAll('input[name^="items"]').forEach(input => {
        input.disabled = false;
    });
}

document.getElementById('add-item').addEventListener('click', function() {
    if (!currentOutletId) return;
    
    const container = document.getElementById('items-container');
    const options = cylinderTypes.map(ct => {
        const stock = stockData[ct.id] || { empty_qty: 0 };
        return '<option value="' + ct.id + '" data-empty="' + stock.empty_qty + '">' + ct.name + ' (' + ct.size_kg + 'kg)</option>';
    }).join('');
    
    const html = `
        <div class="item-row grid grid-cols-4 gap-2 items-center">
            <div>
                <select name="items[${itemIndex}][cylinder_type_id]" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 cylinder-select">
                    <option value="">Select</option>
                    ${options}
                </select>
            </div>
            <div class="text-sm text-gray-600 empty-stock">
                <span class="stock-empty">0</span> available
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
        const emptyQty = selectedOption.dataset.empty || 0;
        row.querySelector('.stock-empty').textContent = emptyQty;
    }
});

document.addEventListener('DOMContentLoaded', function() {
    disableItems(true);
});
</script>
@endsection