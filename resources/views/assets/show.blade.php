@extends('layouts.app')

@section('title', 'Asset Details')

@section('header', 'Asset Details')

@section('content')
<div class="mb-4 flex justify-between items-center">
    <a href="{{ route('assets.index') }}" class="text-indigo-600 hover:underline text-sm">&larr; Back to Asset Register</a>
    <div class="flex gap-2">
        @if($asset->status == 'active')
            <a href="{{ route('assets.edit', $asset) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm">Edit Asset</a>
            <button type="button" onclick="document.getElementById('disposeModal').showModal()" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-sm">Dispose Asset</button>
        @else
            <form action="{{ route('assets.reactivate', $asset) }}" method="POST" onsubmit="return confirm('Reactivate this asset?')">
                @csrf
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">Reactivate</button>
            </form>
        @endif
        @if($asset->status != 'disposed')
            <form action="{{ route('assets.destroy', $asset) }}" method="POST" onsubmit="return confirm('Delete this asset permanently? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-gray-200 text-red-700 px-4 py-2 rounded hover:bg-gray-300 text-sm">Delete Asset</button>
            </form>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="flex justify-between items-start mb-4">
        <div>
            <h3 class="text-lg font-semibold">{{ $asset->name }}</h3>
            <p class="text-sm text-gray-500">{{ $asset->asset_number }}</p>
        </div>
        <span class="px-2 py-1 text-xs rounded
            {{ $asset->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
            {{ ucwords(str_replace('_', ' ', $asset->status)) }}
        </span>
    </div>

    @if($asset->outletAsCar)
        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded text-sm text-blue-800">
            This vehicle is the outlet <a href="{{ route('outlets.edit', $asset->outletAsCar) }}" class="underline font-medium">{{ $asset->outletAsCar->name }}</a>.
        </div>
    @endif

    @if($asset->status == 'disposed')
        <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded text-sm text-gray-700">
            Disposed on {{ $asset->disposed_at?->format('d/m/Y') }}.
            @if($asset->disposal_notes)
                <span class="block mt-1">{{ $asset->disposal_notes }}</span>
            @endif
        </div>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div>
            <p class="text-xs text-gray-500 uppercase">Category</p>
            <p class="font-medium">{{ $asset->category->name ?? 'N/A' }}</p>
            <p class="text-xs text-gray-400">{{ $asset->category->is_depreciable ?? false ? 'Depreciable' : 'Non-depreciable' }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500 uppercase">Location</p>
            <p class="font-medium">{{ $asset->outlet->name ?? $asset->employee->full_name ?? 'HQ' }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500 uppercase">Serial Number</p>
            <p class="font-medium">{{ $asset->serial_number ?? '-' }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500 uppercase">Plate Number</p>
            <p class="font-medium">{{ $asset->plate_number ?? '-' }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500 uppercase">Purchase Date</p>
            <p class="font-medium">{{ $asset->purchase_date?->format('d/m/Y') ?? '-' }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500 uppercase">Purchase Cost</p>
            <p class="font-medium">{{ number_format($asset->purchase_cost, 2) }}</p>
        </div>
        @if($asset->category->is_depreciable ?? false)
            <div>
                <p class="text-xs text-gray-500 uppercase">Depreciation Rate</p>
                <p class="font-medium">{{ number_format($asset->depreciation_rate, 2) }}%</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase">Accumulated Depreciation</p>
                <p class="font-medium">{{ number_format($asset->accumulated_depreciation, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase">Current Book Value</p>
                <p class="font-bold text-lg">{{ number_format($asset->current_book_value, 2) }}</p>
            </div>
        @endif
    </div>
</div>

@if($asset->category->is_depreciable ?? false)
<div class="bg-white rounded-lg shadow overflow-hidden mb-6">
    <div class="px-6 py-3 border-b font-medium">Depreciation History</div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Book Value Before</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Depreciation</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Book Value After</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($asset->depreciationLogs->sortByDesc('period_end') as $log)
                <tr>
                    <td class="px-6 py-4">{{ $log->period_start?->format('d/m/Y') }} - {{ $log->period_end?->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($log->book_value_before, 2) }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($log->depreciation_amount, 2) }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($log->book_value_after, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No depreciation runs yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-3 border-b font-medium flex justify-between items-center">
        <span>Related Expenses</span>
        <a href="{{ url('expenses/create?asset_id=' . $asset->id) }}" class="text-indigo-600 hover:underline text-sm">+ Add Expense</a>
    </div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($asset->expenses->sortByDesc('expense_date') as $expense)
                <tr>
                    <td class="px-6 py-4">{{ $expense->expense_date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">{{ $expense->category->name }}</td>
                    <td class="px-6 py-4">{{ $expense->description }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($expense->amount, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No expenses recorded for this asset yet.</td>
                </tr>
            @endforelse
        </tbody>
        @if($asset->expenses->count() > 0)
        <tfoot class="bg-gray-50 font-semibold">
            <tr>
                <td colspan="3" class="px-6 py-3 text-right">Total</td>
                <td class="px-6 py-3 text-right">{{ number_format($asset->expenses->sum('amount'), 2) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>

@if($asset->status == 'active')
<dialog id="disposeModal" class="rounded-lg shadow-lg p-6 w-full max-w-md">
    <form action="{{ route('assets.dispose', $asset) }}" method="POST">
        @csrf
        <h3 class="text-lg font-semibold mb-4">Dispose Asset</h3>
        <p class="text-sm text-gray-600 mb-4">This marks the asset as disposed and stops it from being used going forward. Its purchase cost, depreciation rate, and book value are left exactly as they are — disposal doesn't rewrite depreciation history.</p>
        @if($asset->outletAsCar)
            <p class="text-sm text-orange-600 mb-4">Its outlet ({{ $asset->outletAsCar->name }}) will also be deactivated.</p>
        @endif
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Disposal Date *</label>
            <input type="date" name="disposed_at" value="{{ date('Y-m-d') }}" required class="form-input">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="disposal_notes" rows="2" class="form-input" placeholder="e.g. sold, written off, scrapped"></textarea>
        </div>
        <div class="flex gap-2 justify-end">
            <button type="button" onclick="document.getElementById('disposeModal').close()" class="px-4 py-2 text-gray-700 border rounded">Cancel</button>
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Confirm Disposal</button>
        </div>
    </form>
</dialog>
@endif
@endsection
