@extends('layouts.app')

@section('title', 'Edit Cylinder Type')

@section('header', 'Edit Cylinder Type')

@section('breadcrumb', 'Configuration > Cylinder Types > Edit')

@section('content')
<form action="{{ route('cylinder-types.update', $cylinderType) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card mb-6">
        <div class="card-body">
            <div class="form-group mb-4">
                <label class="form-label">Name *</label>
                <input type="text" name="name" value="{{ old('name', $cylinderType->name) }}" required class="form-input">
            </div>

            <div class="form-group mb-4">
                <label class="form-label">Size (kg) *</label>
                <input type="number" name="size_kg" value="{{ old('size_kg', $cylinderType->size_kg) }}" required class="form-input">
            </div>
        </div>
    </div>

    <div class="card mb-6">
        <div class="card-header">
            <h3>Pricing</h3>
        </div>
        <div class="card-body">
            <div class="form-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <div class="form-group">
                    <label class="form-label">Full Sale Cost</label>
                    <input type="number" name="full_sale_cost" value="{{ old('full_sale_cost', $cylinderType->full_sale_cost) }}" step="0.01" min="0" required class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Full Sale Price</label>
                    <input type="number" name="full_sale_price" value="{{ old('full_sale_price', $cylinderType->full_sale_price) }}" step="0.01" min="0" required class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Refill Cost</label>
                    <input type="number" name="refill_cost" value="{{ old('refill_cost', $cylinderType->refill_cost) }}" step="0.01" min="0" required class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Refill Price</label>
                    <input type="number" name="refill_price" value="{{ old('refill_price', $cylinderType->refill_price) }}" step="0.01" min="0" required class="form-input">
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-6">
        <div class="card-body">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ $cylinderType->is_active ? 'checked' : '' }}>
                <span>Active</span>
            </label>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('cylinder-types.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>
@endsection