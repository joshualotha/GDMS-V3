@extends('layouts.app')

@section('title', 'Depreciation Report')

@section('header', 'Depreciation Report')

@section('content')
<form method="GET" class="bg-white p-4 rounded-lg shadow mb-4 flex flex-wrap gap-4 items-end">
    <div>
        <label class="block text-xs text-gray-500">Date From</label>
        <input type="date" name="date_from" value="{{ request('date_from', date('Y-m-01')) }}" class="border rounded px-2 py-1">
    </div>
    <div>
        <label class="block text-xs text-gray-500">Date To</label>
        <input type="date" name="date_to" value="{{ request('date_to', date('Y-m-d')) }}" class="border rounded px-2 py-1">
    </div>
    <div>
        <label class="block text-xs text-gray-500">Asset</label>
        <select name="asset_id" class="border rounded px-2 py-1">
            <option value="">All Assets</option>
            @foreach($assets as $asset)
                <option value="{{ $asset->id }}" {{ request('asset_id') == $asset->id ? 'selected' : '' }}>{{ $asset->name }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="bg-gray-600 text-white px-4 py-1 rounded hover:bg-gray-700">Generate</button>
    <a href="{{ route('reports.depreciation.export', request()->query()) }}" class="bg-red-600 text-white px-4 py-1 rounded hover:bg-red-700">Export PDF</a>
</form>

@if(count($summary) > 0)
{{-- Period Summary Section --}}
<div class="bg-white rounded-lg shadow mb-4">
    <div class="px-4 py-3 border-b border-gray-200">
        @php
            $fromMonth = $dateFrom->format('m');
            $toMonth = $dateTo->format('m');
            $fromYear = $dateFrom->format('Y');
            $toYear = $dateTo->format('Y');
            
            if ($fromYear == $toYear && $fromMonth == '01' && $toMonth == '12') {
                $periodLabel = $fromYear . ' (Full Year)';
            } elseif ($fromYear == $toYear && $fromMonth == $toMonth) {
                $periodLabel = $dateFrom->format('F Y');
            } elseif ($fromYear == $toYear) {
                $periodLabel = $dateFrom->format('F') . ' - ' . $dateTo->format('F') . ' ' . $fromYear;
            } else {
                $periodLabel = $dateFrom->format('d M Y') . ' - ' . $dateTo->format('d M Y');
            }
        @endphp
        <h2 class="text-sm font-semibold text-gray-700">Depreciation Summary: {{ $periodLabel }}</h2>
        <p class="text-xs text-gray-500 mt-1">
            Depreciation runs <strong>monthly</strong> using the Reducing Balance method.
            The annual rate is applied each month as:
            <code>monthly rate = (1 - (1 - annual_rate/100)^(1/12)) × 100</code>.
            Below is the aggregated view for the selected period.
        </p>
    </div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Asset</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-600 uppercase">Annual Rate</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-600 uppercase">Opening Book Value</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-600 uppercase">Total Depreciation</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-600 uppercase">Closing Book Value</th>
                <th class="px-4 py-2 text-center text-xs font-medium text-gray-600 uppercase">Periods</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @php $grandTotalDepreciation = 0; @endphp
            @foreach($summary as $s)
                @php $grandTotalDepreciation += $s->total_depreciation; @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $s->asset_name }}</td>
                    <td class="px-4 py-2 text-right text-sm">{{ $s->annual_rate }}%</td>
                    <td class="px-4 py-2 text-right text-sm">{{ number_format($s->opening_book_value, 2) }}</td>
                    <td class="px-4 py-2 text-right text-sm text-red-600">-{{ number_format($s->total_depreciation, 2) }}</td>
                    <td class="px-4 py-2 text-right text-sm">{{ number_format($s->closing_book_value, 2) }}</td>
                    <td class="px-4 py-2 text-center text-sm text-gray-500">{{ $s->entry_count }} {{ $s->entry_count == 1 ? 'period' : 'periods' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-gray-50 font-semibold">
            <tr>
                <td colspan="3" class="px-4 py-2 text-right text-sm text-gray-700">Total for Period:</td>
                <td class="px-4 py-2 text-right text-sm text-red-600">-{{ number_format($grandTotalDepreciation, 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
</div>
@endif

{{-- Monthly Detail Section --}}
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
        <h2 class="text-sm font-semibold text-gray-700">Monthly Depreciation Details</h2>
        <p class="text-xs text-gray-500 mt-1">
            Each row represents one month's depreciation. The <strong>Rate</strong> column shows the
            <strong>annual depreciation rate</strong> (e.g., 2% per year), while the
            <strong>Depreciation</strong> column shows the <strong>monthly amount</strong> calculated
            using the reducing balance method.
        </p>
    </div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Asset</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Period Start</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Book Value Before</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Annual Rate</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Monthly Depreciation</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Book Value After</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($logs as $log)
                <tr>
                    <td class="px-4 py-2">{{ $log->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-2">{{ $log->asset->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $log->period_start?->format('d/m/Y') ?? '-' }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format($log->book_value_before, 2) }}</td>
                    <td class="px-4 py-2 text-right">{{ $log->depreciation_rate ?? 0 }}%</td>
                    <td class="px-4 py-2 text-right text-red-600">-{{ number_format($log->depreciation_amount, 2) }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format($log->book_value_after, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-4 text-center text-gray-500">No depreciation records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection