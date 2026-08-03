@extends('layouts.app')

@section('title', 'Accessories')

@section('header', 'Accessories')

@section('breadcrumb', 'Configuration > Accessories')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Accessories</h1>
        <p>Burners, regulators, hoses, and other gas equipment sold alongside cylinders</p>
    </div>
    <div class="page-header-right">
        <a href="{{ route('accessories.create') }}" class="btn btn-primary">Add Accessory</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="table-container">
    <div class="table-scroll">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>SKU</th>
                <th class="text-right">Cost Price</th>
                <th class="text-right">Sale Price</th>
                <th class="text-right">Margin</th>
                <th class="text-center">Status</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($accessories as $accessory)
                <tr>
                    <td>{{ $accessory->name }}</td>
                    <td>{{ $accessory->sku ?? '-' }}</td>
                    <td class="text-right">{{ number_format($accessory->cost_price, 2) }}</td>
                    <td class="text-right">{{ number_format($accessory->sale_price, 2) }}</td>
                    <td class="text-right">{{ number_format($accessory->margin, 1) }}%</td>
                    <td class="text-center">
                        <form action="{{ route('accessories.toggle', $accessory) }}" method="POST">
                            @csrf
                            <button type="submit" class="badge {{ $accessory->is_active ? 'badge-success' : 'badge-neutral' }}">
                                {{ $accessory->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('accessories.edit', $accessory) }}" class="btn btn-sm btn-secondary">Edit</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No accessories found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection
