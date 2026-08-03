@extends('layouts.app')

@section('title', 'Procurement')

@section('header', 'Procurement')

@section('content')
<div class="page-header">
    <a href="{{ url('warehouse/procurement/create') }}" class="btn btn-primary">+ New Procurement</a>
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
                <th>Supplier</th>
                <th>Items</th>
                <th>Total Cost</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($procurements as $grn)
            <tr>
                <td>{{ ($grn->received_date ?? $grn->created_at)->format('d/m/Y') }}</td>
                <td class="font-medium">{{ $grn->grn_number }}</td>
                <td>{{ $grn->supplier->name }}</td>
                <td>
                    @foreach($grn->items as $item)
                        <span class="badge badge-neutral">{{ $item->cylinderType->name }}: {{ $item->quantity }}</span>
                    @endforeach
                </td>
                <td class="font-medium">{{ number_format($grn->total_cost, 2) }}</td>
                <td><span class="badge {{ $grn->status == 'cancelled' ? 'badge-neutral' : 'badge-success' }}">{{ $grn->status }}</span></td>
                <td>
                    @if($grn->status != 'cancelled')
                        <button type="button" onclick="document.getElementById('cancelGrn{{ $grn->id }}').showModal()" class="text-red-600 hover:underline text-sm">Cancel</button>
                        <dialog id="cancelGrn{{ $grn->id }}" class="rounded shadow-lg p-6">
                            <form action="{{ route('warehouse.procurement.cancel', $grn) }}" method="POST">
                                @csrf
                                <p class="font-medium mb-2">Cancel procurement {{ $grn->grn_number }}?</p>
                                <p class="text-sm text-muted mb-4">This reverses the stock it added. This cannot be undone.</p>
                                <label class="form-label">Reason *</label>
                                <textarea name="reason" required class="form-input mb-4" rows="3"></textarea>
                                <div class="flex gap-2 justify-end">
                                    <button type="button" onclick="document.getElementById('cancelGrn{{ $grn->id }}').close()" class="px-4 py-2 border rounded">Back</button>
                                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Confirm Cancel</button>
                                </div>
                            </form>
                        </dialog>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center p-6 text-muted">No procurement records.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection