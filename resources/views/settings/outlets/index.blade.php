@extends('layouts.app')

@section('title', 'Outlets')

@section('header', 'Outlets')

@section('content')
<div class="mb-4 flex justify-end">
    <a href="{{ route('outlets.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
        Add Outlet
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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plate Number</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($outlets as $outlet)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $outlet->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap capitalize">{{ $outlet->type }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $outlet->location }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $outlet->plate_number ?? '-' }}</td>
                    <td class="px-6 py-4 text-center">
                        <form action="{{ route('outlets.toggle', $outlet) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-2 py-1 text-xs rounded {{ $outlet->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $outlet->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('outlets.edit', $outlet) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No outlets found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection