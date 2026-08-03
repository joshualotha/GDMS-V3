@extends('layouts.app')

@section('title', 'New Accessory Purchase')

@section('header', 'New Accessory Purchase')

@section('content')
<form action="{{ url('warehouse/accessory-purchases') }}" method="POST">
    @csrf

    <div class="table-container mb-6">
        <div class="card-header">
            <h3>Purchase Details</h3>
        </div>
        <div class="card-body">
            <div class="grid-3 gap-6">
                <div class="form-group">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-select">
                        <option value="">Select Supplier (optional)</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Purchase Date <span class="form-label-required">*</span></label>
                    <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}" required class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Receipt Number</label>
                    <input type="text" name="receipt_number" class="form-input" placeholder="Optional">
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
            <h3>Accessories to Receive</h3>
            <span class="text-sm text-muted">Enter quantity for each accessory being received</span>
        </div>
        <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th>Accessory</th>
                    <th class="text-right">Current Stock</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Unit Cost</th>
                    <th class="text-right">Line Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($accessories as $acc)
                <tr>
                    <td class="font-medium">{{ $acc->name }}{{ $acc->sku ? ' ('.$acc->sku.')' : '' }}</td>
                    <td class="text-right">{{ isset($stockMain[$acc->id]) ? $stockMain[$acc->id]->qty : 0 }}</td>
                    <td class="text-right">
                        <input type="hidden" name="items[{{ $loop->index }}][accessory_id]" value="{{ $acc->id }}">
                        <input type="number" name="items[{{ $loop->index }}][quantity]" min="0" value="0" class="form-input text-right" style="width:80px" oninput="calcLine(this)">
                    </td>
                    <td class="text-right">
                        <input type="number" name="items[{{ $loop->index }}][unit_cost]" min="0" step="0.01" value="{{ $acc->cost_price }}" class="form-input text-right unit-cost-input" style="width:100px" oninput="calcLine(this)">
                    </td>
                    <td class="text-right line-total font-medium">0</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t-2">
                    <td colspan="4" class="text-right font-medium">Total Cost</td>
                    <td class="text-right font-bold text-lg grand-total">0</td>
                </tr>
            </tfoot>
        </table>
        </div>
    </div>

    <div class="flex justify-between">
        <a href="{{ url('warehouse/accessory-purchases') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Receive Accessories</button>
    </div>
</form>

<script>
function calcLine(input) {
    var row = input.closest('tr');
    var qty = parseInt(row.querySelector('[name$="[quantity]"]').value) || 0;
    var cost = parseFloat(row.querySelector('.unit-cost-input').value) || 0;
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
