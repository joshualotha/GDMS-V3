<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Asset Register Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
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
        <h1>ASSET REGISTER REPORT</h1>
    </div>

    <table>
        <thead>
            <tr>
                <th>Asset #</th>
                <th>Name</th>
                <th>Category</th>
                <th>Purchase Date</th>
                <th class="text-right">Purchase Cost</th>
                <th class="text-right">Accum. Depr.</th>
                <th class="text-right">Book Value</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $asset)
            <tr>
                <td>{{ $asset->asset_number }}</td>
                <td>{{ $asset->name }}</td>
                <td>{{ $asset->category->name ?? '-' }}</td>
                <td>{{ $asset->purchase_date?->format('d/m/Y') ?? '-' }}</td>
                <td class="text-right">{{ number_format($asset->purchase_cost, 2) }}</td>
                <td class="text-right">{{ number_format($asset->accumulated_depreciation, 2) }}</td>
                <td class="text-right font-bold">{{ number_format($asset->current_book_value, 2) }}</td>
                <td>{{ ucwords(str_replace('_', ' ', $asset->status)) }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center">No assets found.</td></tr>
            @endforelse
        </tbody>
        @if($assets->count() > 0)
        <tfoot>
            <tr class="font-bold">
                <td colspan="4">TOTALS</td>
                <td class="text-right">{{ number_format($totals['total_cost'], 2) }}</td>
                <td class="text-right">{{ number_format($totals['total_depreciation'], 2) }}</td>
                <td class="text-right">{{ number_format($totals['total_book_value'], 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        <p>Generated on {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>