@extends('layouts.app')

@section('title', 'Needs Attention')

@section('header', 'Needs Attention')

@section('content')
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
@endif

<div>
    <h3 class="text-lg font-medium mb-4">Sales Needing Attention</h3>
    <p class="text-sm text-gray-500 mb-4">Most sales are recorded with cash already verified and don't show up here. This list is just the exceptions: nothing submitted yet, or flagged for follow-up.</p>
    @forelse($pendingSales as $sale)
        <div class="bg-white rounded shadow p-4 mb-4">
            <div class="flex justify-between items-start">
                <div>
                    <div class="flex items-center gap-2">
                        <p class="font-medium">{{ $sale->sale_number }}</p>
                        <span class="px-2 py-0.5 text-xs rounded {{ $sale->status == 'queried' ? 'bg-red-100 text-red-800' : 'bg-orange-100 text-orange-800' }}">{{ ucfirst($sale->status) }}</span>
                    </div>
                    <p class="text-sm text-gray-500">{{ $sale->outlet->name }} - {{ $sale->sale_date->format('d/m/Y') }}</p>
                    <p class="text-lg font-bold mt-2">{{ number_format($sale->total_price, 2) }}</p>
                    @if($sale->cash_submitted !== null)
                        <p class="text-sm mt-1">Cash Submitted: <span class="font-medium">{{ number_format($sale->cash_submitted, 2) }}</span></p>
                        <p class="text-sm {{ $sale->cash_variance >= 0 ? 'text-green-600' : 'text-red-600' }}">Variance: {{ number_format($sale->cash_variance, 2) }}</p>
                    @else
                        <p class="text-sm text-orange-600">Cash not yet submitted</p>
                    @endif
                    @if($sale->status == 'queried' && $sale->notes)
                        <p class="text-sm text-red-700 mt-2" style="white-space: pre-line;">{{ $sale->notes }}</p>
                    @endif
                </div>
                <div class="flex gap-2">
                    @if($sale->cash_submitted !== null)
                        <form action="{{ url('approvals/sales/' . $sale->id . '/approve') }}" method="POST">@csrf<button type="submit" class="bg-green-600 text-white px-3 py-1 rounded text-sm">{{ $sale->status == 'queried' ? 'Resolve' : 'Approve' }}</button></form>
                        @if($sale->status == 'pending')
                            <button onclick="document.getElementById('querySale{{ $sale->id }}').showModal()" class="bg-red-600 text-white px-3 py-1 rounded text-sm">Query</button>
                        @endif
                    @else
                        <a href="{{ url('sales/' . $sale->id) }}" class="bg-gray-600 text-white px-3 py-1 rounded text-sm">Enter Cash</a>
                    @endif
                </div>
            </div>
            <dialog id="querySale{{ $sale->id }}" class="rounded shadow-lg p-6">
                <form action="{{ url('approvals/sales/' . $sale->id . '/query') }}" method="POST">
                    @csrf
                    <p class="font-medium mb-4">Reason for query:</p>
                    <textarea name="notes" required class="form-input mb-4" rows="3"></textarea>
                    <div class="flex gap-2 justify-end">
                        <button type="button" onclick="document.getElementById('querySale{{ $sale->id }}').close()" class="px-4 py-2 text-gray-700 border rounded">Cancel</button>
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Submit</button>
                    </div>
                </form>
            </dialog>
        </div>
    @empty
        <p class="text-gray-500">Nothing needs attention right now.</p>
    @endforelse
</div>
@endsection