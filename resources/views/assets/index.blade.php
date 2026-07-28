@extends('layouts.app')

@section('title', 'Asset Register')

@section('header', 'Asset Register')

@section('content')
<div class="mb-4 flex justify-between items-center">
    <div class="flex gap-2">
        <form action="{{ route('assets.catch-up-depreciation') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 text-sm"
                    onclick="return confirm('Run depreciation for all assets that are due? This catches up any missed months automatically.')">
                Run Depreciation
            </button>
        </form>
    </div>
    <a href="{{ route('assets.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
        New Asset
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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asset #</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cost</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Book Value</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($assets as $asset)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">{{ $asset->asset_number }}</td>
                    <td class="px-6 py-4 font-medium">{{ $asset->name }}</td>
                    <td class="px-6 py-4">{{ $asset->category->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $asset->outlet->name ?? $asset->employee->name ?? 'HQ' }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($asset->purchase_cost, 2) }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($asset->current_book_value, 2) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded
                            {{ $asset->status == 'active' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $asset->status == 'disposed' ? 'bg-gray-100 text-gray-800' : '' }}">
                            {{ ucwords(str_replace('_', ' ', $asset->status)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('assets.show', $asset) }}" class="text-indigo-600 hover:text-indigo-900 text-sm mr-3">View</a>
                        @if($asset->status == 'active')
                            <a href="{{ route('assets.edit', $asset) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</a>
                        @else
                            <form action="{{ route('assets.reactivate', $asset) }}" method="POST" class="inline" onsubmit="return confirm('Reactivate this asset?')">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-900 text-sm">Reactivate</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No assets found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection