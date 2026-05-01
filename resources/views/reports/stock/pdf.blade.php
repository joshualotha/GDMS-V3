<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Stock Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        td, th { padding: 6px; border: 1px solid #ddd; text-align: left; }
        th { background: #f5f5f5; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .section-title { background: #e5e5e5; padding: 8px; font-weight: bold; margin-top: 20px; }
        .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>STOCK REPORT</h1>
        <p>As at {{ $date->format('d/m/Y') }}</p>
    </div>

    <div class="section-title">Main Store Stock</div>
    <table>
        <thead>
            <tr>
                <th>Cylinder Type</th>
                <th class="text-right">Full Cylinders</th>
                <th class="text-right">Empty Cylinders</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cylinderTypes as $ct)
            <tr>
                <td>{{ $ct->name }}</td>
                <td class="text-right">{{ $mainStock[$ct->id]['full'] ?? 0 }}</td>
                <td class="text-right">{{ $mainStock[$ct->id]['empty'] ?? 0 }}</td>
                <td class="text-right font-bold">{{ ($mainStock[$ct->id]['full'] ?? 0) + ($mainStock[$ct->id]['empty'] ?? 0) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="font-bold">
                <td>TOTAL</td>
                <td class="text-right">{{ $totalFull }}</td>
                <td class="text-right">{{ $totalEmpty }}</td>
                <td class="text-right">{{ $totalFull + $totalEmpty }}</td>
            </tr>
        </tfoot>
    </table>

    @foreach($outlets as $outlet)
    <div class="section-title">{{ $outlet->name }} - Stock</div>
    <table>
        <thead>
            <tr>
                <th>Cylinder Type</th>
                <th class="text-right">Full</th>
                <th class="text-right">Empty</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cylinderTypes as $ct)
            <tr>
                <td>{{ $ct->name }}</td>
                <td class="text-right">{{ $outletStock[$outlet->id][$ct->id]['full'] ?? 0 }}</td>
                <td class="text-right">{{ $outletStock[$outlet->id][$ct->id]['empty'] ?? 0 }}</td>
                <td class="text-right font-bold">{{ ($outletStock[$outlet->id][$ct->id]['full'] ?? 0) + ($outletStock[$outlet->id][$ct->id]['empty'] ?? 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endforeach

    <div class="footer">
        <p>Generated on {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>