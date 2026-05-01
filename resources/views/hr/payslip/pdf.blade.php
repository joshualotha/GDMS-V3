<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payslip - {{ $item->employee->employee_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0 0; color: #666; }
        .info { display: flex; flex-wrap: wrap; margin-bottom: 30px; }
        .info-item { width: 50%; margin-bottom: 10px; }
        .info-item label { display: block; font-size: 12px; color: #666; }
        .info-item span { display: block; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        td:first-child { text-align: left; color: #666; }
        td:last-child { text-align: right; }
        .total-row { font-weight: bold; font-size: 18px; }
        .total-row td { border-top: 2px solid #333; padding-top: 15px; }
        .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PAYSLIP</h1>
        <p>{{ $item->period->period_name }}</p>
    </div>

    <div class="info">
        <div class="info-item">
            <label>Employee Number</label>
            <span>{{ $item->employee->employee_number }}</span>
        </div>
        <div class="info-item">
            <label>Employee Name</label>
            <span>{{ $item->employee->full_name }}</span>
        </div>
        <div class="info-item">
            <label>Role</label>
            <span>{{ $item->employee->role_title ?? '-' }}</span>
        </div>
        <div class="info-item">
            <label>Outlet</label>
            <span>{{ $item->employee->outlet->name ?? 'HQ' }}</span>
        </div>
    </div>

    <table>
        <tr>
            <td>Basic Salary</td>
            <td>{{ number_format($item->basic_salary, 2) }}</td>
        </tr>
        <tr>
            <td>Allowances</td>
            <td>{{ number_format($item->allowances, 2) }}</td>
        </tr>
        @if($item->allowance_note)
        <tr>
            <td style="font-size: 11px; padding-left: 20px;">{{ $item->allowance_note }}</td>
        </tr>
        @endif
        <tr>
            <td>Deductions</td>
            <td>-{{ number_format($item->deductions, 2) }}</td>
        </tr>
        @if($item->deduction_note)
        <tr>
            <td style="font-size: 11px; padding-left: 20px;">{{ $item->deduction_note }}</td>
        </tr>
        @endif
        <tr class="total-row">
            <td>NET PAY</td>
            <td>{{ number_format($item->net_pay, 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        <p>Status: {{ ucfirst($item->period->status) }}</p>
        <p>Generated on {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>