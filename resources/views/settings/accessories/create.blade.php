@extends('layouts.app')

@section('title', 'Add Accessory')

@section('header', 'Add Accessory')

@section('breadcrumb', 'Configuration > Accessories > Add')

@section('content')
<form action="{{ route('accessories.store') }}" method="POST">
    @csrf

    <div class="card mb-6">
        <div class="card-body">
            <div class="form-group mb-4">
                <label class="form-label">Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="form-input">
            </div>

            <div class="form-group mb-4">
                <label class="form-label">SKU</label>
                <input type="text" name="sku" value="{{ old('sku') }}" class="form-input">
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <div class="form-group">
                    <label class="form-label">Cost Price *</label>
                    <input type="number" name="cost_price" value="{{ old('cost_price', 0) }}" step="0.01" min="0" required class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Sale Price *</label>
                    <input type="number" name="sale_price" value="{{ old('sale_price', 0) }}" step="0.01" min="0" required class="form-input">
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-6">
        <div class="card-body">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <span>Active</span>
            </label>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('accessories.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
@endsection
