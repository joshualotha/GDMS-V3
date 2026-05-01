@extends('layouts.app')

@section('title', 'Suppliers')

@section('header', 'Suppliers')

@section('content')
<div class="mb-4 flex justify-end">
    <a href="{{ route('suppliers.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
        Add Supplier
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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact Person</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($suppliers as $supplier)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $supplier->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $supplier->contact_person ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $supplier->phone ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $supplier->email ?? '-' }}</td>
                    <td class="px-6 py-4 text-center">
                        <form action="{{ route('suppliers.toggle', $supplier) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-2 py-1 text-xs rounded {{ $supplier->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $supplier->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('suppliers.edit', $supplier) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No suppliers found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection