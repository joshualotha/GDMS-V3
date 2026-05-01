@extends('layouts.app')

@section('title', 'Post Opening Stock')

@section('header', 'Post Opening Stock')

@section('breadcrumb', 'Warehouse > Opening Stock')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Post Opening Stock</h1>
        <p>Set initial stock quantities for main store or outlets</p>
    </div>
</div>

<form action="{{ url('warehouse/opening-stock') }}" method="POST">
    @csrf

    <div class="card mb-6">
        <div class="card-header">
            <h3>Stock Location</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Location *</label>
                <select name="outlet_id" id="outlet_id" class="form-select" onchange="toggleMainStoreInfo()">
                    <option value="">Main Store</option>
                    @foreach($outlets as $outlet)
                        <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                    @endforeach
                </select>
                <p class="form-hint">Select an outlet or leave empty for main store</p>
            </div>
        </div>
    </div>

    <div class="card mb-6">
        <div class="card-header">
            <h3>Stock Quantities</h3>
        </div>
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>Cylinder Type</th>
                        <th class="text-right">Full Qty</th>
                        <th class="text-right">Empty Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cylinderTypes as $index => $ct)
                    <tr>
                        <td>
                            <input type="hidden" name="items[{{ $index }}][cylinder_type_id]" value="{{ $ct->id }}">
                            {{ $ct->name }} ({{ $ct->size_kg }}kg)
                        </td>
                        <td class="text-right">
                            <input type="number" name="items[{{ $index }}][full_qty]" min="0" value="0" class="form-input" style="width: 100px; text-align: right;">
                        </td>
                        <td class="text-right">
                            <input type="number" name="items[{{ $index }}][empty_qty]" min="0" value="0" class="form-input" style="width: 100px; text-align: right;">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mb-6">
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea name="notes" rows="2" class="form-input">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ url('warehouse/opening-stock') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Post Opening Stock</button>
    </div>
</form>
@endsection