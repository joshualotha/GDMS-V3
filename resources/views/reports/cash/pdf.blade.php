<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cash Reconciliation Report</title>
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
        .variance { color: red; font-weight: bold; }
        .no-variance { color: green; }
        .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>CASH RECONCILIATION REPORT</h1>
        <p>{{ $dateFrom->format('d/m/Y') }} to {{ $dateTo->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Outlet</th>
                <th>Sale #</th>
                <th class="text-right">Expected</th>
                <th class="text-right">Submitted</th>
                <th class="text-right">Variance</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($submissions as $sale)
            <tr>
                <td>{{ $sale->sale_date->format('d/m/Y') }}</td>
                <td>{{ $sale->outlet->name ?? '-' }}</td>
                <td>{{ $sale->sale_number ?? '-' }}</td>
                <td class="text-right">{{ number_format($sale->total_amount, 2) }}</td>
                <td class="text-right">{{ number_format($sale->cash_submitted, 2) }}</td>
                <td class="text-right {{ $sale->cash_variance != 0 ? 'variance' : 'no-variance' }}">
                    {{ number_format($sale->cash_variance, 2) }}
                </td>
                <td>{{ ucfirst($sale->status) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">No cash submissions found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>