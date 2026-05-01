<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Stock Movement Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        td, th { padding: 5px; border: 1px solid #ddd; text-align: left; }
        th { background: #f5f5f5; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .positive { color: green; }
        .negative { color: red; }
        .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>STOCK MOVEMENT REPORT</h1>
        <p>{{ $dateFrom->format('d/m/Y') }} to {{ $dateTo->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Cylinder</th>
                <th class="text-right">Full Chg</th>
                <th class="text-right">Empty Chg</th>
                <th class="text-right">Full After</th>
                <th class="text-right">Empty After</th>
                <th>Reference</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movements as $movement)
            <tr>
                <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ ucfirst($movement->transaction_type) }}</td>
                <td>{{ $movement->cylinderType->name ?? '-' }}</td>
                <td class="text-right {{ $movement->full_qty_change >= 0 ? 'positive' : 'negative' }}">
                    {{ $movement->full_qty_change >= 0 ? '+' : '' }}{{ $movement->full_qty_change }}
                </td>
                <td class="text-right {{ $movement->empty_qty_change >= 0 ? 'positive' : 'negative' }}">
                    {{ $movement->empty_qty_change >= 0 ? '+' : '' }}{{ $movement->empty_qty_change }}
                </td>
                <td class="text-right">{{ $movement->full_qty_after }}</td>
                <td class="text-right">{{ $movement->empty_qty_after }}</td>
                <td>{{ $movement->reference_type ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">No movements found.</td>
            </tr>
            @endforelse
        </tbody>
        @if($movements->count() > 0)
        <tfoot>
            <tr class="font-bold">
                <td colspan="3">TOTALS</td>
                <td class="text-right {{ $totals['full_change'] >= 0 ? 'positive' : 'negative' }}">
                    {{ $totals['full_change'] >= 0 ? '+' : '' }}{{ $totals['full_change'] }}
                </td>
                <td class="text-right {{ $totals['empty_change'] >= 0 ? 'positive' : 'negative' }}">
                    {{ $totals['empty_change'] >= 0 ? '+' : '' }}{{ $totals['empty_change'] }}
                </td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        <p>Generated on {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>