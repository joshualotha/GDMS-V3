@extends('layouts.app')

@section('title', 'Stock Ledger')

@section('header', 'Stock Ledger')

@section('content')
<div class="table-container mb-6">
    <form method="GET" class="table-filters">
        <div class="form-group">
            <label class="form-label">Location</label>
            <select name="location" class="form-select">
                <option value="">All Locations</option>
                <option value="main" {{ request()->location == 'main' ? 'selected' : '' }}>Main Warehouse</option>
                @foreach($outlets as $outlet)
                <option value="{{ $outlet->id }}" {{ request()->location == $outlet->id ? 'selected' : '' }}>{{ $outlet->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Cylinder Type</label>
            <select name="cylinder_type_id" class="form-select">
                <option value="">All</option>
                @foreach($cylinderTypes as $ct)
                <option value="{{ $ct->id }}" {{ request()->cylinder_type_id == $ct->id ? 'selected' : '' }}>{{ $ct->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Transaction Type</label>
            <select name="transaction_type" class="form-select">
                <option value="">All</option>
                <option value="opening" {{ request()->transaction_type == 'opening' ? 'selected' : '' }}>Opening</option>
                <option value="sale" {{ request()->transaction_type == 'sale' ? 'selected' : '' }}>Sale</option>
                <option value="grn_full" {{ request()->transaction_type == 'grn_full' ? 'selected' : '' }}>GRN Full</option>
                <option value="grn_refill" {{ request()->transaction_type == 'grn_refill' ? 'selected' : '' }}>GRN Refill</option>
                <option value="transfer_out" {{ request()->transaction_type == 'transfer_out' ? 'selected' : '' }}>Transfer Out</option>
                <option value="transfer_in" {{ request()->transaction_type == 'transfer_in' ? 'selected' : '' }}>Transfer In</option>
                <option value="empty_return_in" {{ request()->transaction_type == 'empty_return_in' ? 'selected' : '' }}>Empty Return</option>
                <option value="adjustment" {{ request()->transaction_type == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Date From</label>
            <input type="date" name="date_from" value="{{ request()->date_from }}" class="form-input">
        </div>
        <div class="form-group">
            <label class="form-label">Date To</label>
            <input type="date" name="date_to" value="{{ request()->date_to }}" class="form-input">
        </div>
        <div class="flex items-end">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>
</div>

<div class="table-container">
    <div class="card-header">
        <h3>Stock Movements</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Location</th>
                <th>Cylinder Type</th>
                <th class="text-right">Full Change</th>
                <th class="text-right">Empty Change</th>
                <th class="text-right">Full Balance</th>
                <th class="text-right">Empty Balance</th>
                <th>Type</th>
                <th>Reference</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ledger as $entry)
            <tr>
                <td>{{ $entry->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $entry->outlet_id ? \App\Models\Outlet::find($entry->outlet_id)->name : 'Main Warehouse' }}</td>
                <td>{{ $entry->cylinderType->name }}</td>
                <td class="text-right {{ $entry->full_qty_change >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $entry->full_qty_change >= 0 ? '+' : '' }}{{ $entry->full_qty_change }}
                </td>
                <td class="text-right {{ $entry->empty_qty_change >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $entry->empty_qty_change >= 0 ? '+' : '' }}{{ $entry->empty_qty_change }}
                </td>
                <td class="text-right font-medium">{{ $entry->full_qty_after }}</td>
                <td class="text-right">{{ $entry->empty_qty_after }}</td>
                <td class="capitalize">{{ str_replace('_', ' ', $entry->transaction_type) }}</td>
                <td class="text-muted">{{ $entry->note ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center p-6 text-muted">No movements found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $ledger->appends(request()->query())->links() }}
@endsection