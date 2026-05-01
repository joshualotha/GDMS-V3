@extends('layouts.app')

@section('title', 'Approvals')

@section('header', 'Pending Approvals')

@section('content')
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif

<div>
    <h3 class="text-lg font-medium mb-4">Pending Sales (Awaiting Cash Verification)</h3>
    @forelse($pendingSales as $sale)
        <div class="bg-white rounded shadow p-4 mb-4">
            <div class="flex justify-between items-start">
                <div>
                    <p class="font-medium">{{ $sale->sale_number }}</p>
                    <p class="text-sm text-gray-500">{{ $sale->outlet->name }} - {{ $sale->sale_date->format('d/m/Y') }}</p>
                    <p class="text-lg font-bold mt-2">{{ number_format($sale->total_price, 2) }}</p>
                    @if($sale->cash_submitted)
                        <p class="text-sm mt-1">Cash Submitted: <span class="font-medium">{{ number_format($sale->cash_submitted, 2) }}</span></p>
                        <p class="text-sm {{ $sale->cash_variance >= 0 ? 'text-green-600' : 'text-red-600' }}">Variance: {{ number_format($sale->cash_variance, 2) }}</p>
                    @else
                        <p class="text-sm text-orange-600">Cash not yet submitted</p>
                    @endif
                </div>
                <div class="flex gap-2">
                    <form action="{{ url('approvals/sales/' . $sale->id . '/approve') }}" method="POST">@csrf<button type="submit" class="bg-green-600 text-white px-3 py-1 rounded text-sm">Approve</button></form>
                    <button onclick="document.getElementById('querySale{{ $sale->id }}').showModal()" class="bg-red-600 text-white px-3 py-1 rounded text-sm">Query</button>
                </div>
            </div>
            <dialog id="querySale{{ $sale->id }}" class="rounded shadow-lg p-6">
                <form action="{{ url('approvals/sales/' . $sale->id . '/query') }}" method="POST">
                    @csrf
                    <p class="font-medium mb-4">Reason for query:</p>
                    <textarea name="notes" required class="w-full border rounded p-2 mb-4" rows="3"></textarea>
                    <div class="flex gap-2 justify-end">
                        <button type="button" onclick="document.getElementById('querySale{{ $sale->id }}').close()" class="px-4 py-2 text-gray-700 border rounded">Cancel</button>
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Submit</button>
                    </div>
                </form>
            </dialog>
        </div>
    @empty
        <p class="text-gray-500">No pending sales awaiting approval.</p>
    @endforelse
</div>
@endsection