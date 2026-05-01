@extends('layouts.app')

@section('title', 'Create Goods Received')

@section('header', 'Create Goods Received')

@section('content')
<form action="{{ route('goods-received.store') }}" method="POST" class="space-y-6">
    @csrf

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium mb-4">Receipt Details</h3>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">GRN Number</label>
                <input type="text" value="{{ $grnNumber }}" disabled class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50">
            </div>
            <div>
                <label for="supplier_id" class="block text-sm font-medium text-gray-700">Supplier</label>
                <select name="supplier_id" id="supplier_id" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select Supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label for="purchase_order_id" class="block text-sm font-medium text-gray-700">Select Purchase Order</label>
            <select name="purchase_order_id" id="purchase_order_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Select PO (or enter manually below)</option>
                @foreach(\App\Models\PurchaseOrder::where('status', 'pending')->with('supplier')->get() as $po)
                    <option value="{{ $po->id }}" data-supplier-id="{{ $po->supplier_id }}">{{ $po->po_number }} - {{ $po->supplier->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mt-4">
            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
            <textarea name="notes" id="notes" rows="2"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium mb-4">Received Items</h3>

        <div id="items-container" class="space-y-4">
            <div class="grid grid-cols-6 gap-2 text-sm font-medium text-gray-500">
                <div class="col-span-2">Cylinder Type</div>
                <div>Type</div>
                <div>Quantity</div>
                <div>Unit Cost</div>
                <div>Total</div>
                <div></div>
            </div>

            <div class="item-row grid grid-cols-6 gap-2 items-center">
                <div class="col-span-2">
                    <select name="items[0][cylinder_type_id]" required
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 item-cylinder">
                        <option value="">Select</option>
                        @foreach($cylinderTypes as $ct)
                            <option value="{{ $ct->id }}" data-full-cost="{{ $ct->full_sale_cost ?? 0 }}" data-refill-cost="{{ $ct->refill_cost ?? 0 }}">
                                {{ $ct->name }} ({{ $ct->size_kg }}kg)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="items[0][purchase_type]" required
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 item-type">
                        <option value="full">Full</option>
                        <option value="refill">Refill</option>
                    </select>
                </div>
                <div>
                    <input type="number" name="items[0][quantity]" min="1" value="1" required
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 item-qty">
                </div>
                <div>
                    <input type="number" name="items[0][unit_cost]" step="0.01" min="0" value="0" required
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 item-cost">
                </div>
                <div class="text-right font-medium item-total">0.00</div>
                <div>
                    <button type="button" class="text-red-600 hover:text-red-800 remove-item">Remove</button>
                </div>
            </div>
        </div>

        <button type="button" id="add-item" class="mt-4 text-indigo-600 hover:text-indigo-800">+ Add Item</button>
    </div>

    <div class="flex justify-end gap-4">
        <a href="{{ route('goods-received.index') }}" class="px-4 py-2 text-gray-700 border border-gray-300 rounded hover:bg-gray-50">
            Cancel
        </a>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            Receive Goods
        </button>
    </div>
</form>

<script>
let itemIndex = 1;
const cylinderTypes = @json($cylinderTypes);

function updateCost(row) {
    const cylinderSelect = row.querySelector('.item-cylinder');
    const typeSelect = row.querySelector('.item-type');
    const qtyInput = row.querySelector('.item-qty');
    const costInput = row.querySelector('.item-cost');
    const totalEl = row.querySelector('.item-total');
    
    const selectedOption = cylinderSelect.options[cylinderSelect.selectedIndex];
    const type = typeSelect.value;
    const qty = parseInt(qtyInput.value) || 0;
    
    const cost = type === 'full' 
        ? (parseFloat(selectedOption.dataset.fullCost) || 0)
        : (parseFloat(selectedOption.dataset.refillCost) || 0);
    
    costInput.value = cost.toFixed(2);
    totalEl.textContent = (cost * qty).toFixed(2);
}

function updateTotal(row) {
    const qtyInput = row.querySelector('.item-qty');
    const costInput = row.querySelector('.item-cost');
    const totalEl = row.querySelector('.item-total');
    
    const qty = parseInt(qtyInput.value) || 0;
    const cost = parseFloat(costInput.value) || 0;
    
    totalEl.textContent = (cost * qty).toFixed(2);
}

document.getElementById('purchase_order_id').addEventListener('change', function() {
    const poId = this.value;
    const supplierSelect = document.getElementById('supplier_id');
    
    if (!poId) return;
    
    const selectedOption = this.options[this.options.selectedIndex];
    const supplierId = selectedOption.dataset.supplierId;
    
    if (supplierId) {
        supplierSelect.value = supplierId;
    }
    
    fetch('{{ url("procurement/purchase-orders") }}/' + poId + '/items')
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('items-container');
            
            container.innerHTML = `
                <div class="grid grid-cols-6 gap-2 text-sm font-medium text-gray-500">
                    <div class="col-span-2">Cylinder Type</div>
                    <div>Type</div>
                    <div>Quantity</div>
                    <div>Unit Cost</div>
                    <div>Total</div>
                    <div></div>
                </div>
            `;
            
            itemIndex = 0;
            data.items.forEach(item => {
                const fullCost = parseFloat(item.cylinder_type?.full_sale_cost) || 0;
                const refillCost = parseFloat(item.cylinder_type?.refill_cost) || 0;
                const isFull = item.purchase_type === 'full';
                
                const html = `
                    <div class="item-row grid grid-cols-6 gap-2 items-center">
                        <div class="col-span-2">
                            <select name="items[${itemIndex}][cylinder_type_id]" required
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 item-cylinder">
                                <option value="${item.cylinder_type_id}">${item.cylinder_type?.name || ''} (${item.cylinder_type?.size_kg || 0}kg)</option>
                            </select>
                        </div>
                        <div>
                            <select name="items[${itemIndex}][purchase_type]" required
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 item-type">
                                <option value="full" ${isFull ? 'selected' : ''}>Full</option>
                                <option value="refill" ${!isFull ? 'selected' : ''}>Refill</option>
                            </select>
                        </div>
                        <div>
                            <input type="number" name="items[${itemIndex}][quantity]" min="1" value="${item.quantity}" required
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 item-qty">
                        </div>
                        <div>
                            <input type="number" name="items[${itemIndex}][unit_cost]" step="0.01" min="0" value="${item.unit_cost}" required
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 item-cost">
                        </div>
                        <div class="text-right font-medium item-total">${(item.quantity * item.unit_cost).toFixed(2)}</div>
                        <div>
                            <button type="button" class="text-red-600 hover:text-red-800 remove-item">Remove</button>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', html);
                itemIndex++;
            });
            
            itemIndex = data.items.length;
        })
        .catch(err => console.error('Error loading PO items:', err));
});

document.getElementById('add-item').addEventListener('click', function() {
    const container = document.getElementById('items-container');
    const html = `
        <div class="item-row grid grid-cols-6 gap-2 items-center">
            <div class="col-span-2">
                <select name="items[${itemIndex}][cylinder_type_id]" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 item-cylinder">
                    <option value="">Select</option>
                    @foreach($cylinderTypes as $ct)
                        <option value="{{ $ct->id }}" data-full-cost="{{ $ct->full_sale_cost ?? 0 }}" data-refill-cost="{{ $ct->refill_cost ?? 0 }}">
                            {{ $ct->name }} ({{ $ct->size_kg }}kg)
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="items[${itemIndex}][purchase_type]" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 item-type">
                    <option value="full">Full</option>
                    <option value="refill">Refill</option>
                </select>
            </div>
            <div>
                <input type="number" name="items[${itemIndex}][quantity]" min="1" value="1" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 item-qty">
            </div>
            <div>
                <input type="number" name="items[${itemIndex}][unit_cost]" step="0.01" min="0" value="0" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 item-cost">
            </div>
            <div class="text-right font-medium item-total">0.00</div>
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
    if (e.target.classList.contains('item-cylinder')) {
        updateCost(e.target.closest('.item-row'));
    }
    if (e.target.classList.contains('item-type')) {
        updateCost(e.target.closest('.item-row'));
    }
});

document.addEventListener('input', function(e) {
    if (e.target.classList.contains('item-qty') || e.target.classList.contains('item-cost')) {
        updateTotal(e.target.closest('.item-row'));
    }
});
</script>
@endsection