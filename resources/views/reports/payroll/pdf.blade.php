<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payroll Report</title>
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
        <h1>PAYROLL REPORT</h1>
        <p>{{ $period->period_name }} - {{ ucfirst($period->status) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th class="text-right">Basic Salary</th>
                <th class="text-right">Allowances</th>
                <th class="text-right">Deductions</th>
                <th class="text-right">Net Pay</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
            <tr>
                <td>{{ $item->employee->first_name }} {{ $item->employee->last_name }}</td>
                <td class="text-right">{{ number_format($item->basic_salary, 2) }}</td>
                <td class="text-right">{{ number_format($item->allowances, 2) }}</td>
                <td class="text-right">{{ number_format($item->deductions, 2) }}</td>
                <td class="text-right font-bold">{{ number_format($item->net_pay, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">No payroll items found.</td></tr>
            @endforelse
        </tbody>
        @if($items->count() > 0)
        <tfoot>
            <tr class="font-bold">
                <td>TOTALS</td>
                <td class="text-right">{{ number_format($items->sum('basic_salary'), 2) }}</td>
                <td class="text-right">{{ number_format($items->sum('allowances'), 2) }}</td>
                <td class="text-right">{{ number_format($items->sum('deductions'), 2) }}</td>
                <td class="text-right">{{ number_format($items->sum('net_pay'), 2) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        <p>Generated on {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>