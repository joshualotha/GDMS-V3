@extends('layouts.app')

@section('title', 'Sale Detail')

@section('header', 'Sale: ' . $sale->sale_number)

@section('content')
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="grid grid-cols-2 gap-6">
        <div><p class="text-sm text-gray-500">Outlet</p><p class="font-medium">{{ $sale->outlet->name }}</p></div>
        <div><p class="text-sm text-gray-500">Date</p><p class="font-medium">{{ $sale->sale_date->format('d/m/Y') }}</p></div>
        <div>
            <p class="text-sm text-gray-500">Status</p>
            <p class="font-medium">
                <span class="px-2 py-1 text-xs rounded
                    {{ $sale->status == 'approved' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $sale->status == 'pending' ? 'bg-orange-100 text-orange-800' : '' }}
                    {{ $sale->status == 'queried' ? 'bg-red-100 text-red-800' : '' }}
                    {{ $sale->status == 'cancelled' ? 'bg-gray-200 text-gray-700' : '' }}">
                    {{ ucfirst($sale->status) }}
                </span>
            </p>
        </div>
        <div><p class="text-sm text-gray-500">Expected Cash</p><p class="font-bold text-lg">{{ number_format($sale->total_price, 2) }}</p></div>
    </div>
    @if($sale->status == 'queried' && $sale->notes)
        <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded text-sm text-red-800" style="white-space: pre-line;">{{ $sale->notes }}</div>
    @endif
    @if($sale->status == 'cancelled' && $sale->notes)
        <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded text-sm text-gray-700" style="white-space: pre-line;">{{ $sale->notes }}</div>
    @endif
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cylinder</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Profit</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($sale->items as $item)
                <tr>
                    <td class="px-6 py-4">{{ $item->cylinderType->name }}</td>
                    <td class="px-6 py-4 capitalize">{{ $item->sale_type }}</td>
                    <td class="px-6 py-4 text-right">{{ $item->quantity }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($item->total_price, 2) }}</td>
                    <td class="px-6 py-4 text-right text-green-600">{{ number_format($item->gross_profit, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-gray-50">
            <tr>
                <td colspan="4" class="px-6 py-3 text-right font-medium">Total</td>
                <td class="px-6 py-3 text-right font-bold">{{ number_format($sale->total_price, 2) }}</td>
                <td class="px-6 py-3 text-right font-bold text-green-600">{{ number_format($sale->total_gross_profit, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>

@if($sale->accessoryItems->isNotEmpty())
<div class="bg-white rounded-lg shadow overflow-hidden mt-6">
    <div class="px-6 py-3 border-b font-medium">Accessories</div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Accessory</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Profit</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($sale->accessoryItems as $item)
                <tr>
                    <td class="px-6 py-4">{{ $item->accessory->name }}</td>
                    <td class="px-6 py-4 text-right">{{ $item->quantity }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($item->total_price, 2) }}</td>
                    <td class="px-6 py-4 text-right text-green-600">{{ number_format($item->gross_profit, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-gray-50">
            <tr>
                <td class="px-6 py-3 text-right font-medium">Total</td>
                <td></td>
                <td></td>
                <td class="px-6 py-3 text-right font-bold">{{ number_format($sale->accessoryItems->sum('total_price'), 2) }}</td>
                <td class="px-6 py-3 text-right font-bold text-green-600">{{ number_format($sale->accessoryItems->sum('gross_profit'), 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>
@endif

<div class="bg-white rounded-lg shadow p-6 mt-6">
    <h3 class="text-lg font-semibold mb-4">Cash Verification</h3>

    @if($sale->cash_submitted === null)
        {{-- Step 1: no cash recorded yet --}}
        @if(in_array($sale->status, ['pending', 'queried']))
            <form action="{{ url('sales/' . $sale->id . '/submit-cash') }}" method="POST" enctype="multipart/form-data" class="max-w-sm">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Cash Collected *</label>
                    <input type="number" name="cash_submitted" step="0.01" min="0" required class="mt-1 form-input">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Receipt Photo (optional)</label>
                    <input type="file" name="cash_receipt_image" accept="image/*" class="mt-1 w-full">
                </div>
                <button type="submit" class="btn btn-primary">Submit Cash</button>
            </form>
        @else
            <p class="text-gray-500">No cash was recorded for this sale.</p>
        @endif
    @else
        {{-- Step 2: cash recorded, show it plus variance --}}
        <div class="grid grid-cols-3 gap-6 mb-4">
            <div><p class="text-sm text-gray-500">Cash Submitted</p><p class="font-bold text-lg">{{ number_format($sale->cash_submitted, 2) }}</p></div>
            <div>
                <p class="text-sm text-gray-500">Variance</p>
                <p class="font-bold text-lg {{ $sale->cash_variance >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($sale->cash_variance, 2) }}</p>
            </div>
            <div><p class="text-sm text-gray-500">Submitted On</p><p class="font-medium">{{ $sale->cash_submitted_date?->format('d/m/Y') }}</p></div>
        </div>
        @if($sale->cash_receipt_image)
            <a href="{{ asset('storage/' . $sale->cash_receipt_image) }}" target="_blank" class="text-indigo-600 hover:underline text-sm">View receipt photo</a>
        @endif
    @endif
</div>

<div class="mt-6 flex justify-between">
    <a href="{{ url('sales') }}" class="btn btn-secondary">Back</a>
    <div class="flex gap-2">
        @if($sale->status == 'approved')
            <button type="button" onclick="document.getElementById('querySale').showModal()" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Flag This Sale</button>
        @elseif(in_array($sale->status, ['pending', 'queried']) && $sale->cash_submitted !== null)
            @if($sale->status == 'pending')
                <button type="button" onclick="document.getElementById('querySale').showModal()" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Query</button>
            @endif
            <form action="{{ url('approvals/sales/' . $sale->id . '/approve') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">{{ $sale->status == 'queried' ? 'Resolve & Approve' : 'Approve & Verify' }}</button>
            </form>
        @endif
        @if($sale->status != 'cancelled')
            <button type="button" onclick="document.getElementById('cancelSale').showModal()" class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800">Cancel Sale</button>
        @endif
    </div>
</div>

<dialog id="cancelSale" class="rounded shadow-lg p-6">
    <form action="{{ url('sales/' . $sale->id . '/cancel') }}" method="POST">
        @csrf
        <p class="font-medium mb-2">Cancel this sale?</p>
        <p class="text-sm text-gray-500 mb-4">This will reverse the stock movement it created (restore full cylinders{{ $sale->items->contains('sale_type', 'refill') ? ', remove the empties it took in' : '' }}) and mark it cancelled. This cannot be undone.</p>
        <label class="block text-sm font-medium text-gray-700 mb-1">Reason *</label>
        <textarea name="reason" required class="form-input mb-4" rows="3"></textarea>
        <div class="flex gap-2 justify-end">
            <button type="button" onclick="document.getElementById('cancelSale').close()" class="px-4 py-2 text-gray-700 border rounded">Back</button>
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Confirm Cancel</button>
        </div>
    </form>
</dialog>

<dialog id="querySale" class="rounded shadow-lg p-6">
    <form action="{{ url('approvals/sales/' . $sale->id . '/query') }}" method="POST">
        @csrf
        <p class="font-medium mb-4">Reason for query:</p>
        <textarea name="notes" required class="form-input mb-4" rows="3"></textarea>
        <div class="flex gap-2 justify-end">
            <button type="button" onclick="document.getElementById('querySale').close()" class="px-4 py-2 text-gray-700 border rounded">Cancel</button>
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Submit</button>
        </div>
    </form>
</dialog>
@endsection
