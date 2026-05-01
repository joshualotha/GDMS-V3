<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sales Report</title>
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
        <h1>SALES REPORT</h1>
        <p>{{ $dateFrom->format('d/m/Y') }} to {{ $dateTo->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Outlet</th>
                <th>Cylinder</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Revenue</th>
                <th class="text-right">COGS</th>
                <th class="text-right">Profit</th>
            </tr>
        </thead>
        <tbody>
            @forelse($saleItems as $item)
            <tr>
                <td>{{ $item->sale->sale_date->format('d/m/Y') }}</td>
                <td>{{ $item->sale->outlet->name ?? '-' }}</td>
                <td>{{ $item->cylinderType->name ?? '-' }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->line_total, 2) }}</td>
                <td class="text-right">{{ number_format($item->quantity * $item->unit_cost, 2) }}</td>
                <td class="text-right {{ $item->quantity * ($item->unit_price - $item->unit_cost) >= 0 ? 'positive' : 'negative' }}">
                    {{ number_format($item->quantity * ($item->unit_price - $item->unit_cost), 2) }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">No sales found.</td>
            </tr>
            @endforelse
        </tbody>
        @if($saleItems->count() > 0)
        <tfoot>
            <tr class="font-bold">
                <td colspan="3">TOTALS</td>
                <td class="text-right">{{ $totals['total_qty'] }}</td>
                <td class="text-right">{{ number_format($totals['total_revenue'], 2) }}</td>
                <td class="text-right">{{ number_format($totals['total_cogs'], 2) }}</td>
                <td class="text-right {{ $totals['total_profit'] >= 0 ? 'positive' : 'negative' }}">
                    {{ number_format($totals['total_profit'], 2) }}
                </td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        <p>Generated on {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>