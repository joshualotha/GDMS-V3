@extends('layouts.app')

@section('title', 'Empty Return')

@section('header', 'Return: ' . $emptyReturn->return_number)

@section('content')
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="grid grid-cols-2 gap-6">
        <div>
            <p class="text-sm text-gray-500">Return Number</p>
            <p class="font-medium">{{ $emptyReturn->return_number }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Outlet</p>
            <p class="font-medium">{{ $emptyReturn->outlet->name }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Date</p>
            <p class="font-medium">{{ $emptyReturn->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Status</p>
            <p class="font-medium">{{ ucfirst($emptyReturn->status) }}</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cylinder Type</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity Returned</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($emptyReturn->items as $item)
                <tr>
                    <td class="px-6 py-4">{{ $item->cylinderType->name }} ({{ $item->cylinderType->size_kg }}kg)</td>
                    <td class="px-6 py-4 text-right">{{ $item->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-6 flex justify-end">
    <a href="{{ url('warehouse/empty-returns') }}" class="px-4 py-2 text-gray-700 border border-gray-300 rounded hover:bg-gray-50">Back</a>
</div>
@endsection