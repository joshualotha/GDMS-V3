@extends('layouts.app')

@section('title', 'Stock Adjustments')

@section('header', 'Stock Adjustments')

@section('content')
<div class="mb-4 flex justify-end">
    <a href="{{ url('warehouse/adjustments/create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
        New Adjustment
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Adj #</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cylinder Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Full Change</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Empty Change</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payroll</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($adjustments as $adj)
                <tr>
                    <td class="px-6 py-4">{{ $adj->adjustment_number }}</td>
                    <td class="px-6 py-4">{{ $adj->cylinderType->name }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded {{ $adj->type == 'gain' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($adj->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right {{ $adj->full_qty_change >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $adj->full_qty_change >= 0 ? '+' : '' }}{{ $adj->full_qty_change }}
                    </td>
                    <td class="px-6 py-4 text-right {{ $adj->empty_qty_change >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $adj->empty_qty_change >= 0 ? '+' : '' }}{{ $adj->empty_qty_change }}
                    </td>
                    <td class="px-6 py-4 text-xs">{{ $adj->reason }}</td>
                    <td class="px-6 py-4">{{ $adj->adjustment_date?->format('d/m/Y') ?? $adj->created_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-xs">
                        @if($adj->isOutletLoss())
                            @if($adj->payrollItem)
                                <span class="text-green-700">Deducted ({{ $adj->payrollItem->period->period_name }})</span>
                            @elseif($adj->outlet->employee)
                                <span class="text-orange-600">Pending next payroll</span>
                            @else
                                <span class="text-gray-400">No employee assigned</span>
                            @endif
                        @else
                            <span class="text-gray-300">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($adj->reverses_adjustment_id)
                            <span class="text-xs text-gray-400">Reversal</span>
                        @elseif($adj->reversal)
                            <span class="text-xs text-gray-400">Reversed</span>
                        @else
                            <button type="button" onclick="document.getElementById('reverseModal{{ $adj->id }}').showModal()" class="text-red-600 hover:underline text-sm">Reverse</button>
                            <dialog id="reverseModal{{ $adj->id }}" class="rounded shadow-lg p-6">
                                <form action="{{ route('stock-adjustments.reverse', $adj) }}" method="POST">
                                    @csrf
                                    <p class="font-medium mb-2">Reverse adjustment {{ $adj->adjustment_number }}?</p>
                                    <p class="text-sm text-gray-500 mb-4">This posts an offsetting adjustment to undo its stock effect. The original record stays as-is for the audit trail.</p>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason *</label>
                                    <textarea name="reason" required class="form-input mb-4" rows="3"></textarea>
                                    <div class="flex gap-2 justify-end">
                                        <button type="button" onclick="document.getElementById('reverseModal{{ $adj->id }}').close()" class="px-4 py-2 text-gray-700 border rounded">Back</button>
                                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Confirm Reverse</button>
                                    </div>
                                </form>
                            </dialog>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">No adjustments found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection