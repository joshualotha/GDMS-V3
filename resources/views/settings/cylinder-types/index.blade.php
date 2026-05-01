@extends('layouts.app')

@section('title', 'Cylinder Types')

@section('header', 'Cylinder Types')

@section('breadcrumb', 'Configuration > Cylinder Types')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Cylinder Types</h1>
        <p>Manage your cylinder types and pricing</p>
    </div>
    <div class="page-header-right">
        <a href="{{ route('cylinder-types.create') }}" class="btn btn-primary">Add Cylinder Type</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Size (kg)</th>
                <th class="text-right">Full Cost</th>
                <th class="text-right">Full Price</th>
                <th class="text-right">Full Margin</th>
                <th class="text-right">Refill Cost</th>
                <th class="text-right">Refill Price</th>
                <th class="text-right">Refill Margin</th>
                <th class="text-center">Status</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cylinderTypes as $type)
                <tr>
                    <td>{{ $type->name }}</td>
                    <td>{{ $type->size_kg }} kg</td>
                    <td class="text-right">{{ number_format($type->full_sale_cost, 2) }}</td>
                    <td class="text-right">{{ number_format($type->full_sale_price, 2) }}</td>
                    <td class="text-right">{{ number_format($type->full_sale_margin, 1) }}%</td>
                    <td class="text-right">{{ number_format($type->refill_cost, 2) }}</td>
                    <td class="text-right">{{ number_format($type->refill_price, 2) }}</td>
                    <td class="text-right">{{ number_format($type->refill_margin, 1) }}%</td>
                    <td class="text-center">
                        <form action="{{ route('cylinder-types.toggle', $type) }}" method="POST">
                            @csrf
                            <button type="submit" class="badge {{ $type->is_active ? 'badge-success' : 'badge-neutral' }}">
                                {{ $type->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('cylinder-types.edit', $type) }}" class="btn btn-sm btn-secondary">Edit</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">No cylinder types found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection