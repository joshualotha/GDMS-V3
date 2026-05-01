@extends('layouts.app')

@section('title', 'Procurement')

@section('header', 'Procurement')

@section('content')
<div class="page-header">
    <a href="{{ url('warehouse/procurement/create') }}" class="btn btn-primary">+ New Procurement</a>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Reference</th>
                <th>Supplier</th>
                <th>Items</th>
                <th>Total Cost</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($procurements as $grn)
            <tr>
                <td>{{ $grn->created_at->format('d/m/Y') }}</td>
                <td class="font-medium">{{ $grn->grn_number }}</td>
                <td>{{ $grn->supplier->name }}</td>
                <td>
                    @foreach($grn->items as $item)
                        <span class="badge badge-neutral">{{ $item->cylinderType->name }}: {{ $item->quantity }}</span>
                    @endforeach
                </td>
                <td class="font-medium">{{ number_format($grn->total_cost, 2) }}</td>
                <td><span class="badge badge-success">{{ $grn->status }}</span></td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center p-6 text-muted">No procurement records.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection