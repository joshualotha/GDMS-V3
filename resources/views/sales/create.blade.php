@extends('layouts.app')

@section('title', 'New Sale')

@section('header', 'New Sale')

@section('breadcrumb', 'Operations > Sales > New Sale')

@section('content')
<form action="{{ url('sales') }}" method="POST">
    @csrf

    <div class="card mb-6">
        <div class="card-body">
            <div class="form-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Outlet *</label>
                    <select name="outlet_id" id="outlet_id" required class="form-select" onchange="loadOutletStock()">
                        <option value="">Select Outlet</option>
                        @foreach($outlets as $outlet)
                            <option value="{{ $outlet->id }}">{{ $outlet->name }} ({{ $outlet->type }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Sale Date *</label>
                    <input type="date" name="sale_date" id="sale_date" value="{{ date('Y-m-d') }}" required class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="1" class="form-input">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-6">
        <div class="card-header">
            <h3>Sale Items</h3>
            <button type="button" id="add-item" class="btn btn-sm btn-secondary">+ Add Item</button>
        </div>
        <div class="card-body">
            <div id="items-container">
                <div class="grid grid-cols-6 gap-2 text-sm font-medium" style="color: var(--text-muted); margin-bottom: 8px;">
                    <div class="col-span-2">Cylinder Type</div>
                    <div>Stock</div>
                    <div>Type</div>
                    <div>Qty</div>
                    <div>Price</div>
                </div>
                
                <div class="item-row grid grid-cols-6 gap-2 items-center" style="margin-bottom: 12px;">
                    <div class="col-span-2">
                        <select name="items[0][cylinder_type_id]" required class="form-select" onchange="calculatePrice(this)">
                            <option value="">Select</option>
                            @foreach($cylinderTypes as $ct)
                                <option value="{{ $ct->id }}" data-full-price="{{ $ct->full_sale_price }}" data-refill-price="{{ $ct->refill_price }}">{{ $ct->name }} ({{ $ct->size_kg }}kg)</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <span class="stock-badge">-</span>
                    </div>
                    <div>
                        <select name="items[0][sale_type]" required class="form-select" onchange="calculatePrice(this)">
                            <option value="full">Full Sale</option>
                            <option value="refill">Refill</option>
                        </select>
                    </div>
                    <div>
                        <input type="number" name="items[0][quantity]" min="1" value="1" required class="form-input" oninput="calculatePrice(this)">
                    </div>
                    <div class="text-right font-medium item-price">0.00</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-6">
        <div class="card-body">
            <div class="flex justify-between items-center">
                <div class="text-lg">
                    <strong>Total:</strong> <span id="grand-total" class="text-mono" style="font-size: 20px;">0.00</span>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ url('sales') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Record Sale</button>
    </div>
</form>

<script>
let itemIndex = 1;
var outletStockData = @json($outletStock ?? []);

function loadOutletStock() {
    var outletId = parseInt(document.getElementById('outlet_id').value);
    var stock = outletStockData[outletId] || {};
    updateStockDisplay(stock);
}

function updateStockDisplay(stock) {
    document.querySelectorAll('.item-row').forEach(function(row) {
        var select = row.querySelector('[name$="[cylinder_type_id]"]');
        var badge = row.querySelector('.stock-badge');
        if (!select || !badge) return;
        
        var ctId = select.value;
        var qty = stock[ctId] || 0;
        badge.textContent = ctId ? qty : '-';
        badge.style.fontSize = '12px';
        badge.style.color = qty > 0 ? '#22c55e' : '#ef4444';
    });
}

function calculatePrice(element) {
    var row = element.closest('.item-row');
    var select = row.querySelector('[name$="[cylinder_type_id]"]');
    var saleTypeSelect = row.querySelector('[name$="[sale_type]"]');
    var qtyInput = row.querySelector('[name$="[quantity]"]');
    var priceDisplay = row.querySelector('.item-price');
    var stockBadge = row.querySelector('.stock-badge');
    
    var outletId = parseInt(document.getElementById('outlet_id').value);
    var stock = outletStockData[outletId] || {};
    
    if (stockBadge) {
        var ctId = select.value;
        var qty = stock[ctId] || 0;
        stockBadge.textContent = ctId ? qty : '-';
        stockBadge.style.fontSize = '12px';
        stockBadge.style.color = qty > 0 ? '#22c55e' : '#ef4444';
    }
    
    var selectedOption = select.options[select.selectedIndex];
    var fullPrice = parseFloat(selectedOption.dataset.fullPrice) || 0;
    var refillPrice = parseFloat(selectedOption.dataset.refillPrice) || 0;
    var saleType = saleTypeSelect.value;
    var qty = parseInt(qtyInput.value) || 0;
    
    var unitPrice = saleType === 'full' ? fullPrice : refillPrice;
    var total = unitPrice * qty;
    
    priceDisplay.textContent = total.toFixed(2);
    updateGrandTotal();
}

function updateGrandTotal() {
    let total = 0;
    document.querySelectorAll('.item-price').forEach(el => {
        total += parseFloat(el.textContent) || 0;
    });
    document.getElementById('grand-total').textContent = total.toFixed(2);
}

document.getElementById('add-item').addEventListener('click', function() {
    const container = document.getElementById('items-container');
    const html = `
        <div class="item-row grid grid-cols-6 gap-2 items-center" style="margin-bottom: 12px;">
            <div class="col-span-2">
                <select name="items[${itemIndex}][cylinder_type_id]" required class="form-select" onchange="calculatePrice(this)">
                    <option value="">Select</option>
                    @foreach($cylinderTypes as $ct)
                        <option value="{{ $ct->id }}" data-full-price="{{ $ct->full_sale_price }}" data-refill-price="{{ $ct->refill_price }}">{{ $ct->name }} ({{ $ct->size_kg }}kg)</option>
                    @endforeach
                </select>
            </div>
            <div>
                <span class="stock-badge">-</span>
            </div>
            <div>
                <select name="items[${itemIndex}][sale_type]" required class="form-select" onchange="calculatePrice(this)">
                    <option value="full">Full Sale</option>
                    <option value="refill">Refill</option>
                </select>
            </div>
            <div>
                <input type="number" name="items[${itemIndex}][quantity]" min="1" value="1" required class="form-input" oninput="calculatePrice(this)">
            </div>
            <div class="text-right font-medium item-price">0.00</div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    itemIndex++;
});
</script>
@endsection