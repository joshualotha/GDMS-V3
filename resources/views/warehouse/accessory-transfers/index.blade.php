@extends('layouts.app')

@section('title', 'Accessory Transfers')

@section('header', 'Accessory Transfers')

@section('content')
<div class="page-header">
    <a href="{{ url('warehouse/accessory-transfers/create') }}" class="btn btn-primary">+ New Transfer</a>
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
                <th>Accessories</th>
                <th>Status</th>
                <th>Notes</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($transfers as $transfer)
            <tr>
                <td>{{ $transfer->transfer_date->format('d/m/Y') }}</td>
                <td class="font-medium">{{ $transfer->transfer_number }}</td>
                <td>{{ $transfer->outlet->name }}</td>
                <td>
                    @foreach($transfer->items as $item)
                        <span class="badge badge-neutral">{{ $item->accessory->name }}: {{ $item->quantity }}</span>
                    @endforeach
                </td>
                <td>
                    <span class="badge {{ $transfer->status == 'cancelled' ? 'badge-neutral' : 'badge-success' }}">{{ $transfer->status }}</span>
                </td>
                <td class="text-muted">{{ $transfer->notes ?? '-' }}</td>
                <td>
                    @if($transfer->status != 'cancelled')
                        <button type="button" onclick="document.getElementById('cancelTransfer{{ $transfer->id }}').showModal()" class="text-red-600 hover:underline text-sm">Cancel</button>
                        <dialog id="cancelTransfer{{ $transfer->id }}" class="rounded shadow-lg p-6">
                            <form action="{{ route('accessory-transfers.cancel', $transfer) }}" method="POST">
                                @csrf
                                <p class="font-medium mb-2">Cancel this transfer?</p>
                                <p class="text-sm text-muted mb-4">This reverses the stock it moved. This cannot be undone.</p>
                                <label class="form-label">Reason *</label>
                                <textarea name="reason" required class="form-input mb-4" rows="3"></textarea>
                                <div class="flex gap-2 justify-end">
                                    <button type="button" onclick="document.getElementById('cancelTransfer{{ $transfer->id }}').close()" class="px-4 py-2 border rounded">Back</button>
                                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Confirm Cancel</button>
                                </div>
                            </form>
                        </dialog>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center p-6 text-muted">No transfers found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection
