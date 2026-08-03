@extends('layouts.app')

@section('title', 'New Sale')

@section('header', 'New Sale')

@section('breadcrumb', 'Operations > Sales > New Sale')

@section('content')
<form action="{{ url('sales') }}" method="POST" enctype="multipart/form-data" id="sale-form">
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
            <h3>Cylinders</h3>
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
                        <select name="items[0][cylinder_type_id]" class="form-select" onchange="calculatePrice(this)">
                            <option value="">None</option>
                            @foreach($cylinderTypes as $ct)
                                <option value="{{ $ct->id }}" data-full-price="{{ $ct->full_sale_price }}" data-refill-price="{{ $ct->refill_price }}">{{ $ct->name }} ({{ $ct->size_kg }}kg)</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <span class="stock-badge">-</span>
                    </div>
                    <div>
                        <select name="items[0][sale_type]" class="form-select" onchange="calculatePrice(this)">
                            <option value="full">Full Sale</option>
                            <option value="refill">Refill</option>
                        </select>
                    </div>
                    <div>
                        <input type="number" name="items[0][quantity]" min="1" value="1" class="form-input" oninput="calculatePrice(this)">
                    </div>
                    <div class="text-right font-medium item-price">0.00</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-6">
        <div class="card-header">
            <h3>Accessories</h3>
            <button type="button" id="add-accessory-item" class="btn btn-sm btn-secondary">+ Add Accessory</button>
        </div>
        <div class="card-body">
            <div id="accessory-items-container">
                <div class="grid grid-cols-4 gap-2 text-sm font-medium" style="color: var(--text-muted); margin-bottom: 8px;">
                    <div class="col-span-2">Accessory</div>
                    <div>Stock</div>
                    <div>Qty</div>
                </div>

                <div class="accessory-item-row grid grid-cols-4 gap-2 items-center" style="margin-bottom: 12px;">
                    <div class="col-span-2">
                        <select name="accessory_items[0][accessory_id]" class="form-select" onchange="calculateAccessoryPrice(this)">
                            <option value="">None</option>
                            @foreach($accessories as $acc)
                                <option value="{{ $acc->id }}" data-price="{{ $acc->sale_price }}">{{ $acc->name }}{{ $acc->sku ? ' ('.$acc->sku.')' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <span class="accessory-stock-badge">-</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="number" name="accessory_items[0][quantity]" min="1" value="1" class="form-input" oninput="calculateAccessoryPrice(this)">
                        <span class="text-right font-medium accessory-item-price" style="min-width: 70px;">0.00</span>
                    </div>
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

    <div class="card mb-6">
        <div class="card-header">
            <h3>Deposit &amp; Receipt</h3>
        </div>
        <div class="card-body">
            <p class="form-hint mb-4">If the outlet has already banked the cash and sent you the slip, enter it now &mdash; the sale is recorded and verified in one go. Leave blank to add it later from the sale's page.</p>
            <div class="form-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Amount Deposited</label>
                    <input type="number" name="cash_submitted" id="cash_submitted" step="0.01" min="0" class="form-input" placeholder="Matches total by default">
                </div>
                <div class="form-group">
                    <label class="form-label">Receipt / Deposit Slip Photo</label>
                    <input type="file" name="cash_receipt_image" accept="image/*" class="form-input">
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
let accessoryItemIndex = 1;
var outletStockData = @json($outletStock ?? []);
var outletAccessoryStockData = @json($outletAccessoryStock ?? []);

function loadOutletStock() {
    var outletId = parseInt(document.getElementById('outlet_id').value);
    var stock = outletStockData[outletId] || {};
    var accessoryStock = outletAccessoryStockData[outletId] || {};
    updateStockDisplay(stock);
    updateAccessoryStockDisplay(accessoryStock);
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

function updateAccessoryStockDisplay(stock) {
    document.querySelectorAll('.accessory-item-row').forEach(function(row) {
        var select = row.querySelector('[name$="[accessory_id]"]');
        var badge = row.querySelector('.accessory-stock-badge');
        if (!select || !badge) return;

        var accId = select.value;
        var qty = stock[accId] || 0;
        badge.textContent = accId ? qty : '-';
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
    var total = select.value ? unitPrice * qty : 0;

    priceDisplay.textContent = total.toFixed(2);
    updateGrandTotal();
}

function calculateAccessoryPrice(element) {
    var row = element.closest('.accessory-item-row');
    var select = row.querySelector('[name$="[accessory_id]"]');
    var qtyInput = row.querySelector('[name$="[quantity]"]');
    var priceDisplay = row.querySelector('.accessory-item-price');
    var stockBadge = row.querySelector('.accessory-stock-badge');

    var outletId = parseInt(document.getElementById('outlet_id').value);
    var stock = outletAccessoryStockData[outletId] || {};

    if (stockBadge) {
        var accId = select.value;
        var qty = stock[accId] || 0;
        stockBadge.textContent = accId ? qty : '-';
        stockBadge.style.fontSize = '12px';
        stockBadge.style.color = qty > 0 ? '#22c55e' : '#ef4444';
    }

    var selectedOption = select.options[select.selectedIndex];
    var price = parseFloat(selectedOption.dataset.price) || 0;
    var qty = parseInt(qtyInput.value) || 0;

    var total = select.value ? price * qty : 0;

    priceDisplay.textContent = total.toFixed(2);
    updateGrandTotal();
}

var cashFieldTouched = false;
document.getElementById('cash_submitted').addEventListener('input', function() {
    cashFieldTouched = true;
});

function updateGrandTotal() {
    let total = 0;
    document.querySelectorAll('.item-price').forEach(el => {
        total += parseFloat(el.textContent) || 0;
    });
    document.querySelectorAll('.accessory-item-price').forEach(el => {
        total += parseFloat(el.textContent) || 0;
    });
    document.getElementById('grand-total').textContent = total.toFixed(2);

    if (!cashFieldTouched) {
        document.getElementById('cash_submitted').value = total > 0 ? total.toFixed(2) : '';
    }
}

document.getElementById('add-item').addEventListener('click', function() {
    const container = document.getElementById('items-container');
    const html = `
        <div class="item-row grid grid-cols-6 gap-2 items-center" style="margin-bottom: 12px;">
            <div class="col-span-2">
                <select name="items[${itemIndex}][cylinder_type_id]" class="form-select" onchange="calculatePrice(this)">
                    <option value="">None</option>
                    @foreach($cylinderTypes as $ct)
                        <option value="{{ $ct->id }}" data-full-price="{{ $ct->full_sale_price }}" data-refill-price="{{ $ct->refill_price }}">{{ $ct->name }} ({{ $ct->size_kg }}kg)</option>
                    @endforeach
                </select>
            </div>
            <div>
                <span class="stock-badge">-</span>
            </div>
            <div>
                <select name="items[${itemIndex}][sale_type]" class="form-select" onchange="calculatePrice(this)">
                    <option value="full">Full Sale</option>
                    <option value="refill">Refill</option>
                </select>
            </div>
            <div>
                <input type="number" name="items[${itemIndex}][quantity]" min="1" value="1" class="form-input" oninput="calculatePrice(this)">
            </div>
            <div class="text-right font-medium item-price">0.00</div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    itemIndex++;
});

document.getElementById('add-accessory-item').addEventListener('click', function() {
    const container = document.getElementById('accessory-items-container');
    const html = `
        <div class="accessory-item-row grid grid-cols-4 gap-2 items-center" style="margin-bottom: 12px;">
            <div class="col-span-2">
                <select name="accessory_items[${accessoryItemIndex}][accessory_id]" class="form-select" onchange="calculateAccessoryPrice(this)">
                    <option value="">None</option>
                    @foreach($accessories as $acc)
                        <option value="{{ $acc->id }}" data-price="{{ $acc->sale_price }}">{{ $acc->name }}{{ $acc->sku ? ' ('.$acc->sku.')' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <span class="accessory-stock-badge">-</span>
            </div>
            <div class="flex items-center gap-2">
                <input type="number" name="accessory_items[${accessoryItemIndex}][quantity]" min="1" value="1" class="form-input" oninput="calculateAccessoryPrice(this)">
                <span class="text-right font-medium accessory-item-price" style="min-width: 70px;">0.00</span>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    accessoryItemIndex++;
});

// Rows left with nothing selected shouldn't be submitted at all — disabling their
// inputs excludes them from the POST instead of sending an empty/invalid line.
document.getElementById('sale-form').addEventListener('submit', function () {
    document.querySelectorAll('.item-row').forEach(function (row) {
        var select = row.querySelector('[name$="[cylinder_type_id]"]');
        if (!select.value) {
            row.querySelectorAll('select, input').forEach(function (el) { el.disabled = true; });
        }
    });
    document.querySelectorAll('.accessory-item-row').forEach(function (row) {
        var select = row.querySelector('[name$="[accessory_id]"]');
        if (!select.value) {
            row.querySelectorAll('select, input').forEach(function (el) { el.disabled = true; });
        }
    });
});
</script>
@endsection
