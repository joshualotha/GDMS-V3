@extends('layouts.app')

@section('title', 'Stock Position')

@section('header', 'Stock Position')

@section('content')
<div class="grid-2 gap-6 mb-6">
    <div class="stat-card">
        <div class="stat-label">Main Warehouse</div>
        <div class="stat-value">{{ $mainStock->sum('full_qty') }}</div>
        <div class="stat-sublabel">Total Full Cylinders</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">All Outlets</div>
        <div class="stat-value">{{ collect($stockData)->flatten()->sum('full_qty') }}</div>
        <div class="stat-sublabel">Total Full Cylinders</div>
    </div>
</div>

<div class="table-container mb-6">
    <div class="card-header">
        <h3>Main Warehouse Stock</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>Cylinder Type</th>
                <th class="text-right">Full Qty</th>
                <th class="text-right">Empty Qty</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cylinderTypes as $ct)
            @php
                $stock = isset($mainStock[$ct->id]) ? $mainStock[$ct->id] : null;
                $full = $stock ? $stock->full_qty : 0;
                $empty = $stock ? $stock->empty_qty : 0;
            @endphp
            <tr>
                <td>{{ $ct->name }}</td>
                <td class="text-right {{ $full == 0 ? 'text-danger font-semibold' : '' }}">{{ $full }}</td>
                <td class="text-right">{{ $empty }}</td>
                <td class="text-right font-medium">{{ $full + $empty }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@foreach($outlets as $outlet)
<div class="table-container mb-6">
    <div class="card-header">
        <h3>{{ $outlet->name }} ({{ ucfirst($outlet->type) }})</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>Cylinder Type</th>
                <th class="text-right">Full Qty</th>
                <th class="text-right">Empty Qty</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cylinderTypes as $ct)
            @php
                $stock = isset($stockData[$outlet->id][$ct->id]) ? $stockData[$outlet->id][$ct->id] : null;
                $full = $stock ? $stock->full_qty : 0;
                $empty = $stock ? $stock->empty_qty : 0;
            @endphp
            <tr>
                <td>{{ $ct->name }}</td>
                <td class="text-right {{ $full == 0 ? 'text-danger font-semibold' : '' }}">{{ $full }}</td>
                <td class="text-right">{{ $empty }}</td>
                <td class="text-right font-medium">{{ $full + $empty }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endforeach
@endsection