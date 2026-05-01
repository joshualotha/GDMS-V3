<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Depreciation Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0; color: #666; }
        .header .note { font-size: 9px; color: #888; margin-top: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        td, th { padding: 5px; border: 1px solid #ddd; text-align: left; }
        th { background: #f5f5f5; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .section-title { font-size: 13px; font-weight: bold; margin: 15px 0 5px 0; padding: 5px 0; border-bottom: 2px solid #333; }
        .subtitle { font-size: 10px; color: #666; margin-bottom: 10px; }
        .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #666; }
        .grand-total { font-weight: bold; background: #f9f9f9; }
    </style>
</head>
<body>
    <div class="header">
        <h1>DEPRECIATION REPORT</h1>
        <p>{{ $dateFrom->format('d/m/Y') }} to {{ $dateTo->format('d/m/Y') }}</p>
        <p class="note">
            Depreciation is calculated <strong>monthly</strong> using the Reducing Balance method.
            The annual rate is converted to a monthly rate. Below is the annual summary followed by monthly details.
        </p>
    </div>

    @if(count($summary) > 0)
    {{-- Annual Summary --}}
    <div class="section-title">Annual Summary</div>
    <table>
        <thead>
            <tr>
                <th>Asset</th>
                <th class="text-right">Annual Rate</th>
                <th class="text-right">Opening Book Value</th>
                <th class="text-right">Total Depreciation</th>
                <th class="text-right">Closing Book Value</th>
                <th class="text-center">Entries</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotalDepreciation = 0; @endphp
            @foreach($summary as $s)
                @php $grandTotalDepreciation += $s->total_depreciation; @endphp
            <tr>
                <td>{{ $s->asset_name }}</td>
                <td class="text-right">{{ $s->annual_rate }}%</td>
                <td class="text-right">{{ number_format($s->opening_book_value, 2) }}</td>
                <td class="text-right">-{{ number_format($s->total_depreciation, 2) }}</td>
                <td class="text-right">{{ number_format($s->closing_book_value, 2) }}</td>
                <td class="text-center">{{ $s->entry_count }} months</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="grand-total">
                <td colspan="3" class="text-right">Total Depreciation for Period:</td>
                <td class="text-right">-{{ number_format($grandTotalDepreciation, 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    {{-- Monthly Details --}}
    <div class="section-title">Monthly Depreciation Details</div>
    <div class="subtitle">Each row represents one month. The "Annual Rate" column shows the yearly rate; the "Monthly Depreciation" column shows the calculated amount for that month.</div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Asset</th>
                <th>Period Start</th>
                <th class="text-right">Book Value Before</th>
                <th class="text-right">Annual Rate</th>
                <th class="text-right">Monthly Depreciation</th>
                <th class="text-right">Book Value After</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td>{{ $log->created_at->format('d/m/Y') }}</td>
                <td>{{ $log->asset->name ?? '-' }}</td>
                <td>{{ $log->period_start?->format('d/m/Y') ?? '-' }}</td>
                <td class="text-right">{{ number_format($log->book_value_before, 2) }}</td>
                <td class="text-right">{{ $log->depreciation_rate ?? 0 }}%</td>
                <td class="text-right">-{{ number_format($log->depreciation_amount, 2) }}</td>
                <td class="text-right">{{ number_format($log->book_value_after, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center">No depreciation records found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>