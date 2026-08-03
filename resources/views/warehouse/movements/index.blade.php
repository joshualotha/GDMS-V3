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

@if(session('error'))
    <div class="alert alert-danger mb-4">{{ session('error') }}</div>
@endif

@if(session('success'))
    <div class="alert alert-success mb-4">{{ session('success') }}</div>
@endif

<div class="table-container">
    <div class="table-scroll">
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Reference</th>
                <th>Outlet</th>
                <th>Cylinders</th>
                <th>Status</th>
                <th>Notes</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($movements as $movement)
            <tr>
                <td>{{ ($movement->transfer_date ?? $movement->return_date ?? $movement->created_at)->format('d/m/Y') }}</td>
                <td class="font-medium">{{ $movement->transfer_number ?? $movement->return_number }}</td>
                <td>{{ $movement->outlet->name }}</td>
                <td>
                    @foreach($movement->items as $item)
                        <span class="badge badge-neutral">{{ $item->cylinderType->name }}: {{ $item->quantity }}</span>
                    @endforeach
                </td>
                <td>
                    <span class="badge {{ $movement->status == 'cancelled' ? 'badge-neutral' : 'badge-success' }}">{{ $movement->status }}</span>
                </td>
                <td class="text-muted">{{ $movement->notes ?? '-' }}</td>
                <td>
                    @if($movement->status != 'cancelled')
                        <button type="button" onclick="document.getElementById('cancelModal{{ $type }}{{ $movement->id }}').showModal()" class="text-red-600 hover:underline text-sm">Cancel</button>
                        <dialog id="cancelModal{{ $type }}{{ $movement->id }}" class="rounded shadow-lg p-6">
                            <form action="{{ $type == 'return' ? route('warehouse.movements.cancelReturn', $movement) : route('warehouse.movements.cancelTransfer', $movement) }}" method="POST">
                                @csrf
                                <p class="font-medium mb-2">Cancel this {{ $type == 'return' ? 'return' : 'transfer' }}?</p>
                                <p class="text-sm text-muted mb-4">This reverses the stock it moved. This cannot be undone.</p>
                                <label class="form-label">Reason *</label>
                                <textarea name="reason" required class="form-input mb-4" rows="3"></textarea>
                                <div class="flex gap-2 justify-end">
                                    <button type="button" onclick="document.getElementById('cancelModal{{ $type }}{{ $movement->id }}').close()" class="px-4 py-2 border rounded">Back</button>
                                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Confirm Cancel</button>
                                </div>
                            </form>
                        </dialog>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center p-6 text-muted">No movements found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection