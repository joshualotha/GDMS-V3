@extends('layouts.app')

@section('title', 'Stock Adjustment')

@section('header', 'Stock Adjustment')

@section('content')
<form action="{{ url('warehouse/adjustments') }}" method="POST" class="max-w-xl bg-white rounded-lg shadow p-6 space-y-6">
    @csrf

    <div>
        <label for="outlet_id" class="block text-sm font-medium text-gray-700">Outlet / Warehouse</label>
        <select name="outlet_id" id="outlet_id" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Select Outlet</option>
            <option value="main">Main Warehouse</option>
            @foreach($outlets as $outlet)
                <option value="{{ $outlet->id }}">{{ $outlet->name }} ({{ $outlet->type }})</option>
            @endforeach
        </select>
        <input type="hidden" name="is_main" id="is_main" value="0">
    </div>

    <div>
        <label for="cylinder_type_id" class="block text-sm font-medium text-gray-700">Cylinder Type</label>
        <select name="cylinder_type_id" id="cylinder_type_id" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50">
            <option value="">Select outlet first</option>
        </select>
    </div>

    <div id="current-stock" class="bg-gray-50 rounded p-3 text-sm">
        <div class="text-gray-500">Current Stock: <span class="font-medium">Full: </span><span id="stock-full">-</span> | <span class="font-medium">Empty: </span><span id="stock-empty">-</span></div>
    </div>

    <div>
        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
        <select name="type" id="type" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="gain">Gain / Correction (+)</option>
            <option value="loss">Loss / Damage (-)</option>
        </select>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="full_qty_change" class="block text-sm font-medium text-gray-700">Full Qty Change</label>
            <input type="number" name="full_qty_change" id="full_qty_change" value="0"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50" readonly>
        </div>
        <div>
            <label for="empty_qty_change" class="block text-sm font-medium text-gray-700">Empty Qty Change</label>
            <input type="number" name="empty_qty_change" id="empty_qty_change" value="0"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50" readonly>
        </div>
    </div>

    <div>
        <label for="reason" class="block text-sm font-medium text-gray-700">Reason</label>
        <textarea name="reason" id="reason" rows="2" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50" readonly></textarea>
    </div>

    <div class="flex justify-end gap-4">
        <a href="{{ url('warehouse/adjustments') }}" class="px-4 py-2 text-gray-700 border border-gray-300 rounded hover:bg-gray-50">
            Cancel
        </a>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 disabled:opacity-50">
            Submit Adjustment
        </button>
    </div>
</form>

<script>
const cylinderTypes = @json($cylinderTypes);
const stockMain = @json($stockMain);

(function() {
    const cylinderSelect = document.getElementById('cylinder_type_id');
    const fullInput = document.getElementById('full_qty_change');
    const emptyInput = document.getElementById('empty_qty_change');
    const reasonInput = document.getElementById('reason');
    const submitBtn = document.querySelector('button[type="submit"]');
    
    cylinderSelect.classList.add('bg-gray-50');
    fullInput.classList.add('bg-gray-50');
    fullInput.readOnly = true;
    emptyInput.classList.add('bg-gray-50');
    emptyInput.readOnly = true;
    reasonInput.classList.add('bg-gray-50');
    reasonInput.readOnly = true;
    submitBtn.disabled = true;
})();

document.getElementById('outlet_id').addEventListener('change', function() {
    const outletId = this.value;
    const cylinderSelect = document.getElementById('cylinder_type_id');
    const fullInput = document.getElementById('full_qty_change');
    const emptyInput = document.getElementById('empty_qty_change');
    const reasonInput = document.getElementById('reason');
    const submitBtn = document.querySelector('button[type="submit"]');
    const isMainInput = document.getElementById('is_main');
    
    if (!outletId) {
        cylinderSelect.innerHTML = '<option value="">Select outlet first</option>';
        cylinderSelect.classList.add('bg-gray-50');
        fullInput.classList.add('bg-gray-50');
        fullInput.readOnly = true;
        emptyInput.classList.add('bg-gray-50');
        emptyInput.readOnly = true;
        reasonInput.classList.add('bg-gray-50');
        reasonInput.readOnly = true;
        submitBtn.disabled = true;
        document.getElementById('stock-full').textContent = '-';
        document.getElementById('stock-empty').textContent = '-';
        return;
    }
    
    isMainInput.value = outletId === 'main' ? 1 : 0;
    
    cylinderSelect.classList.remove('bg-gray-50');
    fullInput.classList.remove('bg-gray-50');
    fullInput.readOnly = false;
    emptyInput.classList.remove('bg-gray-50');
    emptyInput.readOnly = false;
    reasonInput.classList.remove('bg-gray-50');
    reasonInput.readOnly = false;
    submitBtn.disabled = false;
    
    const options = '<option value="">Select</option>' + cylinderTypes.map(ct => {
        let stock;
        if (outletId === 'main') {
            stock = stockMain[ct.id] || { full_qty: 0, empty_qty: 0 };
        } else {
            stock = { full_qty: 0, empty_qty: 0 };
        }
        return '<option value="' + ct.id + '" data-full="' + stock.full_qty + '" data-empty="' + stock.empty_qty + '">' + ct.name + ' (' + ct.size_kg + 'kg)</option>';
    }).join('');
    
    cylinderSelect.innerHTML = options;
    
    document.getElementById('stock-full').textContent = '-';
    document.getElementById('stock-empty').textContent = '-';
});

document.getElementById('cylinder_type_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    document.getElementById('stock-full').textContent = selectedOption.dataset.full || 0;
    document.getElementById('stock-empty').textContent = selectedOption.dataset.empty || 0;
});
</script>
@endsection