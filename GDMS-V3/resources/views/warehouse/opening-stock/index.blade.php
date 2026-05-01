@extends('layouts.app')
<?php use Illuminate\Support\Facades\Session; ?>

@section('title', 'Opening Stock')

@section('header', 'Opening Stock')

@section('breadcrumb', 'Warehouse > Opening Stock')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Opening Stock</h1>
        <p>Initial stock quantities for main store and outlets</p>
    </div>
    <div class="page-header-right">
        <a href="{{ url('warehouse/opening-stock/create') }}" class="btn btn-primary">Post Opening Stock</a>
    </div>
</div>

@if(Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
@endif

@if(Session::has('error'))
    <div class="alert alert-danger">{{ Session::get('error') }}</div>
@endif

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Reference</th>
                <th>Location</th>
                <th>Date</th>
                <th>Items</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($openingStocks as $os)
                <tr>
                    <td>{{ $os->reference }}</td>
                    <td>
                        @if($os->outlet)
                            <span class="badge badge-info">{{ $os->outlet->name }}</span>
                        @else
                            <span class="badge badge-success">Main Store</span>
                        @endif
                    </td>
                    <td>{{ $os->created_at->format('d/m/Y') }}</td>
                    <td>{{ $os->items->sum('full_qty') }} full, {{ $os->items->sum('empty_qty') }} empty</td>
                    <td>{{ $os->notes ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No opening stock posted yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection