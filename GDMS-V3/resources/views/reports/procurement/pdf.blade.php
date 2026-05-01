<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Procurement Report</title>
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
        .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PROCUREMENT REPORT</h1>
        <p>{{ $dateFrom->format('d/m/Y') }} to {{ $dateTo->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>GR Number</th>
                <th>Supplier</th>
                <th>Cylinder</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Unit Cost</th>
                <th class="text-right">Total Cost</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
            <tr>
                <td>{{ $item->goodsReceived->received_date ? $item->goodsReceived->received_date->format('d/m/Y') : '-' }}</td>
                <td>{{ $item->goodsReceived->gr_number ?? '-' }}</td>
                <td>{{ $item->goodsReceived->supplier->name ?? '-' }}</td>
                <td>{{ $item->cylinderType->name ?? '-' }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->unit_cost, 2) }}</td>
                <td class="text-right">{{ number_format($item->line_total, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">No procurements found.</td>
            </tr>
            @endforelse
        </tbody>
        @if($items->count() > 0)
        <tfoot>
            <tr class="font-bold">
                <td colspan="4">TOTALS</td>
                <td class="text-right">{{ $totalQty }}</td>
                <td></td>
                <td class="text-right">{{ number_format($totalCost, 2) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        <p>Generated on {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>