<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Fuel Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0; color: #666; }
        .section { margin-bottom: 20px; }
        .section-title { background: #e5e5e5; padding: 8px; font-weight: bold; }
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
        <h1>FUEL REPORT</h1>
        <p>{{ $dateFrom->format('d/m/Y') }} to {{ $dateTo->format('d/m/Y') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Section 1: Purchases</div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Fuel Type</th>
                    <th class="text-right">Litres</th>
                    <th class="text-right">Cost</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $purchase)
                <tr>
                    <td>{{ $purchase->created_at->format('d/m/Y') }}</td>
                    <td>{{ ucfirst($purchase->fuel_type) }}</td>
                    <td class="text-right">{{ number_format($purchase->litres, 2) }}</td>
                    <td class="text-right">{{ number_format($purchase->total_cost, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center">No purchases found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Section 2: Issues</div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Vehicle</th>
                    <th>Fuel Type</th>
                    <th class="text-right">Litres</th>
                    <th class="text-right">Odometer</th>
                </tr>
            </thead>
            <tbody>
                @forelse($issues as $issue)
                <tr>
                    <td>{{ $issue->created_at->format('d/m/Y') }}</td>
                    <td>{{ $issue->asset->name ?? '-' }}</td>
                    <td>{{ ucfirst($issue->fuel_type) }}</td>
                    <td class="text-right">{{ number_format($issue->litres, 2) }}</td>
                    <td class="text-right">{{ $issue->odometer_km ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">No issues found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Section 3: Balance Summary</div>
        <table>
            <tr><td>Diesel Balance</td><td class="text-right font-bold">{{ number_format($balance['diesel'], 2) }} L</td></tr>
            <tr><td>Petrol Balance</td><td class="text-right font-bold">{{ number_format($balance['petrol'], 2) }} L</td></tr>
        </table>
    </div>

    <div class="footer">
        <p>Generated on {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>