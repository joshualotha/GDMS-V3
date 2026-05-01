<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Profit & Loss Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        td { padding: 5px; border-bottom: 1px solid #ddd; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .section { background: #e5e5e5; font-weight: bold; }
        .positive { color: green; }
        .negative { color: red; }
        .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PROFIT & LOSS REPORT</h1>
        <p>{{ $dateFrom->format('d/m/Y') }} to {{ $dateTo->format('d/m/Y') }}</p>
    </div>

    <table>
        <tr class="section"><td colspan="2">REVENUE</td></tr>
        <tr><td class="pl-4">Total Sales Revenue</td><td class="text-right">{{ number_format($totalRevenue, 2) }}</td></tr>
        <tr class="font-bold"><td>TOTAL REVENUE</td><td class="text-right">{{ number_format($totalRevenue, 2) }}</td></tr>
        
        <tr class="section"><td colspan="2">COST OF GOODS SOLD</td></tr>
        <tr class="font-bold"><td>TOTAL COGS</td><td class="text-right negative">-{{ number_format($totalCogs, 2) }}</td></tr>
        
        <tr class="section"><td colspan="2">GROSS PROFIT</td></tr>
        <tr class="font-bold" style="font-size:14px;"><td>GROSS PROFIT</td><td class="text-right {{ $grossProfit >= 0 ? 'positive' : 'negative' }}">{{ number_format($grossProfit, 2) }}</td></tr>
        
        <tr class="section"><td colspan="2">OPERATING EXPENSES</td></tr>
        <tr><td class="pl-4">Fuel Costs</td><td class="text-right negative">-{{ number_format($fuelCost, 2) }}</td></tr>
        <tr><td class="pl-4">Payroll Costs</td><td class="text-right negative">-{{ number_format($payrollCost, 2) }}</td></tr>
        <tr><td class="pl-4">Asset Depreciation</td><td class="text-right negative">-{{ number_format($depreciationCost, 2) }}</td></tr>
        <tr><td class="pl-4">Other Expenses</td><td class="text-right negative">-{{ number_format($otherExpenses, 2) }}</td></tr>
        <tr class="font-bold"><td>TOTAL OPERATING EXPENSES</td><td class="text-right negative">-{{ number_format($totalOperatingExpenses, 2) }}</td></tr>
        
        <tr class="font-bold" style="background:#ccc;font-size:14px;"><td>NET PROFIT / (LOSS)</td><td class="text-right {{ $netProfit >= 0 ? 'positive' : 'negative' }}">{{ number_format($netProfit, 2) }}</td></tr>
    </table>

    <div class="footer">
        <p>Generated on {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>