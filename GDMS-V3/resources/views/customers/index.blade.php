@extends('layouts.app')

@section('title', 'Customers')

@section('header', 'Customers')

@section('content')
<div class="mb-4 flex justify-end">
    <a href="{{ route('customers.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
        Add Customer
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Orders</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($customers as $customer)
                <tr>
                    <td class="px-6 py-4">{{ $customer->name }}</td>
                    <td class="px-6 py-4">{{ $customer->phone ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded 
                            {{ $customer->type == 'walk_in' ? 'bg-gray-100 text-gray-800' : '' }}
                            {{ $customer->type == 'regular' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $customer->type == 'wholesale' ? 'bg-purple-100 text-purple-800' : '' }}">
                            {{ ucfirst($customer->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">{{ $customer->sales_count }}</td>
                    <td class="px-6 py-4 text-right">
                        @if($customer->sales_sum_total_price)
                            <span class="text-green-600">{{ number_format($customer->sales_sum_total_price, 2) }}</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded {{ $customer->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $customer->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('customers.edit', $customer) }}" class="text-indigo-600 hover:underline">Edit</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No customers found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection