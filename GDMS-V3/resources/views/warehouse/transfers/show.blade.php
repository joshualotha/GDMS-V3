@extends('layouts.app')

@section('title', 'Transfer Slip')

@section('header', 'Transfer: ' . $stockTransfer->transfer_number)

@section('content')
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="grid grid-cols-2 gap-6">
        <div>
            <p class="text-sm text-gray-500">Transfer Number</p>
            <p class="font-medium">{{ $stockTransfer->transfer_number }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Outlet</p>
            <p class="font-medium">{{ $stockTransfer->outlet->name }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Date</p>
            <p class="font-medium">{{ $stockTransfer->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Status</p>
            <p class="font-medium">{{ ucfirst($stockTransfer->status) }}</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cylinder Type</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($stockTransfer->items as $item)
                <tr>
                    <td class="px-6 py-4">{{ $item->cylinderType->name }} ({{ $item->cylinderType->size_kg }}kg)</td>
                    <td class="px-6 py-4 text-right">{{ $item->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-6 flex justify-end print:hidden">
    <button onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Print</button>
</div>

<style>
@media print {
    aside, header { display: none; }
    .print\:hidden { display: none; }
}
</style>
@endsection