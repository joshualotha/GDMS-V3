@extends('layouts.app')

@section('title', 'Stock Movements')

@section('header', 'Stock Movements')

@section('content')
<div class="page-header">
    <div class="flex gap-4 items-center">
        <a href="{{ url('warehouse/movements?type=transfer') }}" class="btn {{ $type == 'transfer' ? 'btn-primary' : 'btn-secondary' }}">Transfers</a>
        <a href="{{ url('warehouse/movements?type=return') }}" class="btn {{ $type == 'return' ? 'btn-primary' : 'btn-secondary' }}">Empty Returns</a>
        <a href="{{ url('warehouse/movements/create?type=' . $type) }}" class="btn btn-primary">+ New {{ $type == 'transfer' ? 'Transfer' : 'Return' }}</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success mb-4">{{ session('success') }}</div>
@endif

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Reference</th>
                <th>Outlet</th>
                <th>Cylinders</th>
                <th>Status</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movements as $movement)
            <tr>
                <td>{{ $movement->created_at->format('d/m/Y') }}</td>
                <td class="font-medium">{{ $movement->transfer_number ?? $movement->return_number }}</td>
                <td>{{ $movement->outlet->name }}</td>
                <td>
                    @foreach($movement->items as $item)
                        <span class="badge badge-neutral">{{ $item->cylinderType->name }}: {{ $item->quantity }}</span>
                    @endforeach
                </td>
                <td><span class="badge badge-success">{{ $movement->status }}</span></td>
                <td class="text-muted">{{ $movement->notes ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center p-6 text-muted">No movements found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection