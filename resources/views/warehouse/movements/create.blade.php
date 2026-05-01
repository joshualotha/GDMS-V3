@extends('layouts.app')

@section('title', 'New ' . ($type == 'transfer' ? 'Transfer' : 'Empty Return'))

@section('header', 'New ' . ($type == 'transfer' ? 'Transfer' : 'Empty Return'))

@section('content')
<form action="{{ url('warehouse/movements') }}" method="POST">
    @csrf
    <input type="hidden" name="type" value="{{ $type }}">

    <div class="table-container mb-6">
        <div class="card-header">
            <h3>{{ $type == 'transfer' ? 'Transfer Details' : 'Empty Return Details' }}</h3>
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
                    <label class="form-label">Notes</label>
                    <input type="text" name="notes" class="form-input" placeholder="Optional notes">
                </div>
            </div>
        </div>
    </div>

    <div class="table-container mb-6">
        <div class="card-header flex justify-between items-center">
            <h3>Cylinders</h3>
            <span class="text-sm text-muted">Select outlet to see stock levels</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Cylinder</th>
                    <th class="text-right">Main (Full)</th>
                    <th class="text-right">Outlet (Full)</th>
                    <th class="text-right">Outlet (Empty)</th>
                    <th class="text-right">Qty</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cylinderTypes as $index => $ct)
                <tr>
                    <td class="font-medium">{{ $ct->name }} ({{ $ct->size_kg }}kg)</td>
                    <td class="text-right main-stock" id="main-{{ $ct->id }}">
                        {{ isset($stockMain[$ct->id]) ? $stockMain[$ct->id]->full_qty : 0 }}
                    </td>
                    <td class="text-right outlet-full" id="outlet-full-{{ $ct->id }}">-</td>
                    <td class="text-right outlet-empty" id="outlet-empty-{{ $ct->id }}">-</td>
                    <td class="text-right">
                        <input type="hidden" name="items[{{ $index }}][cylinder_type_id]" value="{{ $ct->id }}">
                        <input type="number" name="items[{{ $index }}][quantity]" min="0" value="0" class="form-input text-right" style="width:80px" oninput="updateTotal()">
                    </td>
                    <td>
                        @if($type == 'return')
                        <span class="badge badge-warning">Empty</span>
                        @else
                        <span class="badge badge-primary">Full</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ url('warehouse/movements?type=' . $type) }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">{{ $type == 'transfer' ? 'Transfer' : 'Record Return' }}</button>
    </div>
</form>

<script>
document.getElementById('outlet_id').addEventListener('change', function() {
    var outletId = this.value;
    if (!outletId) {
        @foreach($cylinderTypes as $ct)
        document.getElementById('outlet-full-{{ $ct->id }}').textContent = '-';
        document.getElementById('outlet-empty-{{ $ct->id }}').textContent = '-';
        @endforeach
        return;
    }
    
    fetch('/warehouse/movements/stock/' + outletId)
        .then(res => res.json())
        .then(function(data) {
            @foreach($cylinderTypes as $ct)
            var stock = data[{{ $ct->id }}] || { full_qty: 0, empty_qty: 0 };
            document.getElementById('outlet-full-{{ $ct->id }}').textContent = stock.full_qty;
            document.getElementById('outlet-empty-{{ $ct->id }}').textContent = stock.empty_qty;
            @endforeach
        });
});
</script>
@endsection