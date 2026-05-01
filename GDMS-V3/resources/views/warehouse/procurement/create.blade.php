@extends('layouts.app')

@section('title', 'New Procurement (GRN)')

@section('header', 'New Procurement (GRN)')

@section('content')
<form action="{{ url('warehouse/procurement') }}" method="POST">
    @csrf

    <div class="table-container mb-6">
        <div class="card-header">
            <h3>Supplier & Details</h3>
        </div>
        <div class="card-body">
            <div class="grid-3 gap-6">
                <div class="form-group">
                    <label class="form-label">Supplier <span class="form-label-required">*</span></label>
                    <select name="supplier_id" required class="form-select">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">GRN Number</label>
                    <input type="text" class="form-input" value="GRN-{{ date('Ymd') }}-0001" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">Notes</label>
                    <input type="text" name="notes" class="form-input" placeholder="Optional notes">
                </div>
            </div>
        </div>
    </div>

    <div class="table-container mb-6">
        <div class="card-header flex justify-between items-center">
            <h3>Cylinders to Receive</h3>
            <span class="text-sm text-muted">Enter quantity for each type being received</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Cylinder</th>
                    <th class="text-right">Current</th>
                    <th>Type</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Unit Cost</th>
                    <th class="text-right">Line Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cylinderTypes as $ct)
                <tr data-full-cost="{{ $ct->full_sale_cost ?? 0 }}" data-refill-cost="{{ $ct->refill_cost ?? 0 }}">
                    <td class="font-medium">{{ $ct->name }} ({{ $ct->size_kg }}kg)</td>
                    <td class="text-right">{{ isset($stockMain[$ct->id]) ? $stockMain[$ct->id]->full_qty : 0 }} full<br><span class="text-muted">{{ isset($stockMain[$ct->id]) ? $stockMain[$ct->id]->empty_qty : 0 }} empty</span></td>
                    <td>
                        <select name="items[{{ $loop->index }}][purchase_type]" class="form-select" onchange="updateCost(this)">
                            <option value="full">Full</option>
                            <option value="refill">Refill</option>
                        </select>
                    </td>
                    <td class="text-right">
                        <input type="hidden" name="items[{{ $loop->index }}][cylinder_type_id]" value="{{ $ct->id }}">
                        <input type="number" name="items[{{ $loop->index }}][quantity]" min="0" value="0" class="form-input text-right" style="width:80px" oninput="calcLine(this)">
                    </td>
                    <td class="text-right">
                        <input type="hidden" name="items[{{ $loop->index }}][unit_cost]" value="{{ $ct->full_sale_cost ?: $ct->refill_cost ?: 0 }}">
                        <span class="unit-cost-display font-medium">{{ number_format($ct->full_sale_cost ?: $ct->refill_cost ?: 0) }}</span>
                    </td>
                    <td class="text-right line-total font-medium">0</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t-2">
                    <td colspan="5" class="text-right font-medium">Total Cost</td>
                    <td class="text-right font-bold text-lg grand-total">0</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="flex justify-between">
        <a href="{{ url('warehouse/procurement') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Receive Goods</button>
    </div>
</form>

<script>
function updateCost(select) {
    var row = select.closest('tr');
    var type = select.value;
    var fullCost = parseFloat(row.dataset.fullCost) || 0;
    var refillCost = parseFloat(row.dataset.refillCost) || 0;
    var cost = type === 'full' ? fullCost : refillCost;
    row.querySelector('input[name$=\"[unit_cost]\"]').value = cost;
    row.querySelector('.unit-cost-display').textContent = cost.toLocaleString();
    calcLine(row);
}

function calcLine(input) {
    var row;
    if (input.tagName === 'TR') {
        row = input;
    } else {
        row = input.closest('tr');
    }
    var qty = parseInt(row.querySelector('[name$=\"[quantity]\"]').value) || 0;
    var cost = parseFloat(row.querySelector('input[name$=\"[unit_cost]\"]').value) || 0;
    row.querySelector('.line-total').textContent = (qty * cost).toLocaleString();
    calcGrandTotal();
}

function calcGrandTotal() {
    var total = 0;
    document.querySelectorAll('.line-total').forEach(function(el) {
        total += parseFloat(el.textContent.replace(/,/g, '')) || 0;
    });
    document.querySelector('.grand-total').textContent = total.toLocaleString();
}

calcGrandTotal();
</script>
@endsection