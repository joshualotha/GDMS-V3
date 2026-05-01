@extends('layouts.app')

@section('title', 'Empty Returns')

@section('header', 'Empty Returns')

@section('content')
<div class="mb-4 flex justify-end">
    <a href="{{ url('warehouse/empty-returns/create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
        New Empty Return
    </a>
</div>

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
@endif

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Return #</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Outlet</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($returns as $return)
                <tr>
                    <td class="px-6 py-4">{{ $return->return_number }}</td>
                    <td class="px-6 py-4">{{ $return->outlet->name }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded {{ $return->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($return->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">{{ $return->created_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ url('warehouse/empty-returns/' . $return->id) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No returns found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection