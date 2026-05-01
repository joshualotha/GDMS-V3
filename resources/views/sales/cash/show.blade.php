@extends('layouts.app')

@section('title', 'Cash Submission')

@section('header', 'Submission: ' . $cashSubmission->reference)

@section('content')
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="grid grid-cols-2 gap-6">
        <div><p class="text-sm text-gray-500">Sale</p><p class="font-medium">{{ $cashSubmission->sale->sale_number }}</p></div>
        <div><p class="text-sm text-gray-500">Outlet</p><p class="font-medium">{{ $cashSubmission->sale->outlet->name }}</p></div>
        <div><p class="text-sm text-gray-500">Expected</p><p class="font-medium">{{ number_format($cashSubmission->expected_amount, 2) }}</p></div>
        <div><p class="text-sm text-gray-500">Submitted</p><p class="font-medium">{{ number_format($cashSubmission->submitted_amount, 2) }}</p></div>
        <div><p class="text-sm text-gray-500">Variance</p><p class="font-medium {{ $cashSubmission->variance >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($cashSubmission->variance, 2) }}</p></div>
        <div><p class="text-sm text-gray-500">Status</p><p class="font-medium">{{ ucfirst($cashSubmission->status) }}</p></div>
    </div>
</div>

@if($cashSubmission->receipt_image)
<div class="mb-6">
    <p class="text-sm text-gray-500 mb-2">Receipt</p>
    <img src="{{ asset('storage/' . $cashSubmission->receipt_image) }}" class="max-w-md rounded shadow">
</div>
@endif

<div class="mt-6 flex justify-between">
    <a href="{{ url('cash-submissions') }}" class="px-4 py-2 text-gray-700 border border-gray-300 rounded hover:bg-gray-50">Back</a>
    @if($cashSubmission->status == 'pending')
        <div class="flex gap-2">
            <form action="{{ url('cash-submissions/' . $cashSubmission->id . '/approve') }}" method="POST">@csrf<button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Approve</button></form>
            <button onclick="document.getElementById('queryModal').showModal()" class="bg-red-600 text-white px-4 py-2 rounded">Query</button>
        </div>
    @endif
</div>

<dialog id="queryModal" class="rounded shadow p-6">
    <form action="{{ url('cash-submissions/' . $cashSubmission->id . '/query') }}" method="POST">
        @csrf
        <p class="font-medium mb-4">Reason for query:</p>
        <textarea name="note" required class="w-full border rounded p-2 mb-4" rows="3"></textarea>
        <div class="flex gap-2 justify-end">
            <button type="button" onclick="document.getElementById('queryModal').close()" class="px-4 py-2 text-gray-700 border rounded">Cancel</button>
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Submit Query</button>
        </div>
    </form>
</dialog>
@endsection