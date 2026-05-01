@extends('layouts.app')

@section('title', 'Asset Register Report')

@section('header', 'Asset Register Report')

@section('content')
<form method="GET" class="bg-white p-4 rounded-lg shadow mb-4 flex flex-wrap gap-4 items-end">
    <div>
        <label class="block text-xs text-gray-500">Category</label>
        <select name="asset_category_id" class="border rounded px-2 py-1">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('asset_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500">Status</label>
        <select name="status" class="border rounded px-2 py-1">
            <option value="">All Status</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="under_maintenance" {{ request('status') == 'under_maintenance' ? 'selected' : '' }}>Under Maintenance</option>
            <option value="disposed" {{ request('status') == 'disposed' ? 'selected' : '' }}>Disposed</option>
        </select>
    </div>
    <button type="submit" class="bg-gray-600 text-white px-4 py-1 rounded hover:bg-gray-700">Generate</button>
    <a href="{{ route('reports.asset.export', request()->query()) }}" class="bg-red-600 text-white px-4 py-1 rounded hover:bg-red-700">Export PDF</a>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Asset #</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Purchase Date</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Purchase Cost</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Accum. Depr.</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Book Value</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($assets as $asset)
                <tr>
                    <td class="px-4 py-2">{{ $asset->asset_number }}</td>
                    <td class="px-4 py-2">{{ $asset->name }}</td>
                    <td class="px-4 py-2">{{ $asset->category->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $asset->purchase_date?->format('d/m/Y') ?? '-' }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format($asset->purchase_cost, 2) }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format($asset->accumulated_depreciation, 2) }}</td>
                    <td class="px-4 py-2 text-right font-semibold">{{ number_format($asset->current_book_value, 2) }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 text-xs rounded 
                            {{ $asset->status == 'active' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $asset->status == 'under_maintenance' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $asset->status == 'disposed' ? 'bg-gray-100 text-gray-800' : '' }}">
                            {{ ucwords(str_replace('_', ' ', $asset->status)) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-4 text-center text-gray-500">No assets found.</td>
                </tr>
            @endforelse
        </tbody>
        @if($assets->count() > 0)
        <tfoot class="bg-gray-50 font-semibold">
            <tr>
                <td colspan="4" class="px-4 py-2">TOTALS</td>
                <td class="px-4 py-2 text-right">{{ number_format($totals['total_cost'], 2) }}</td>
                <td class="px-4 py-2 text-right">{{ number_format($totals['total_depreciation'], 2) }}</td>
                <td class="px-4 py-2 text-right">{{ number_format($totals['total_book_value'], 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
@endsection