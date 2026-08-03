@extends('layouts.app')

@section('title', 'New Accessory Transfer')

@section('header', 'New Accessory Transfer')

@section('content')
<form action="{{ url('warehouse/accessory-transfers') }}" method="POST">
    @csrf

    <div class="table-container mb-6">
        <div class="card-header">
            <h3>Transfer Details</h3>
        </div>
        <div class="card-body">
            <div class="grid-2 gap-6">
                <div class="form-group">
                    <label class="form-label">Outlet <span class="form-label-required">*</span></label>
                    <select name="outlet_id" id="outlet_id" required class="form-select">
                        <option value="">Select Outlet</option>
                        @foreach($outlets as $outlet)
                        <option value="{{ $outlet->id }}">{{ $outlet->name }} ({{ $outlet->type }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Date <span class="form-label-required">*</span></label>
                    <input type="date" name="transfer_date" value="{{ old('transfer_date', date('Y-m-d')) }}" required class="form-input">
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
            <h3>Accessories</h3>
            <span class="text-sm text-muted">Select outlet to see current stock levels</span>
        </div>
        <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th>Accessory</th>
                    <th class="text-right">Main Stock</th>
                    <th class="text-right">Outlet Stock</th>
                    <th class="text-right">Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($accessories as $index => $acc)
                <tr>
                    <td class="font-medium">{{ $acc->name }}{{ $acc->sku ? ' ('.$acc->sku.')' : '' }}</td>
                    <td class="text-right main-stock" id="main-{{ $acc->id }}">
                        {{ isset($stockMain[$acc->id]) ? $stockMain[$acc->id]->qty : 0 }}
                    </td>
                    <td class="text-right outlet-stock" id="outlet-{{ $acc->id }}">-</td>
                    <td class="text-right">
                        <input type="hidden" name="items[{{ $index }}][accessory_id]" value="{{ $acc->id }}">
                        <input type="number" name="items[{{ $index }}][quantity]" min="0" value="0" class="form-input text-right" style="width:80px">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ url('warehouse/accessory-transfers') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Transfer</button>
    </div>
</form>

<script>
document.getElementById('outlet_id').addEventListener('change', function() {
    var outletId = this.value;
    if (!outletId) {
        @foreach($accessories as $acc)
        document.getElementById('outlet-{{ $acc->id }}').textContent = '-';
        @endforeach
        return;
    }

    fetch('/warehouse/accessory-transfers/stock/' + outletId)
        .then(res => res.json())
        .then(function(data) {
            @foreach($accessories as $acc)
            var stock = data[{{ $acc->id }}] || { qty: 0 };
            document.getElementById('outlet-{{ $acc->id }}').textContent = stock.qty;
            @endforeach
        });
});
</script>
@endsection
