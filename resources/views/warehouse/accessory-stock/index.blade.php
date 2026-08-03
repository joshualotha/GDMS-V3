@extends('layouts.app')

@section('title', 'Accessory Stock')

@section('header', 'Accessory Stock')

@section('content')
<div class="grid-2 gap-6 mb-6">
    <div class="stat-card">
        <div class="stat-label">Main Warehouse</div>
        <div class="stat-value">{{ $mainStock->sum('qty') }}</div>
        <div class="stat-sublabel">Total Accessories</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">All Outlets</div>
        <div class="stat-value">{{ collect($stockData)->flatten()->sum('qty') }}</div>
        <div class="stat-sublabel">Total Accessories</div>
    </div>
</div>

<div class="table-container mb-6">
    <div class="card-header">
        <h3>Main Warehouse Stock</h3>
    </div>
    <div class="table-scroll">
    <table>
        <thead>
            <tr>
                <th>Accessory</th>
                <th class="text-right">Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach($accessories as $acc)
            @php
                $stock = isset($mainStock[$acc->id]) ? $mainStock[$acc->id] : null;
                $qty = $stock ? $stock->qty : 0;
            @endphp
            <tr>
                <td>{{ $acc->name }}{{ $acc->sku ? ' ('.$acc->sku.')' : '' }}</td>
                <td class="text-right {{ $qty == 0 ? 'text-danger font-semibold' : '' }}">{{ $qty }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>

@foreach($outlets as $outlet)
<div class="table-container mb-6">
    <div class="card-header">
        <h3>{{ $outlet->name }} ({{ ucfirst($outlet->type) }})</h3>
    </div>
    <div class="table-scroll">
    <table>
        <thead>
            <tr>
                <th>Accessory</th>
                <th class="text-right">Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach($accessories as $acc)
            @php
                $stock = isset($stockData[$outlet->id][$acc->id]) ? $stockData[$outlet->id][$acc->id] : null;
                $qty = $stock ? $stock->qty : 0;
            @endphp
            <tr>
                <td>{{ $acc->name }}{{ $acc->sku ? ' ('.$acc->sku.')' : '' }}</td>
                <td class="text-right {{ $qty == 0 ? 'text-danger font-semibold' : '' }}">{{ $qty }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>
@endforeach
@endsection
