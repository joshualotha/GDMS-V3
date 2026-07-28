<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\PayrollItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PayslipController extends Controller
{
    public function show(PayrollItem $item)
    {
        $item->load('employee', 'period');

        if ($item->period->status === 'draft') {
            abort(403, 'Payslip is not available until the payroll period is approved.');
        }

        return view('hr.payslip.show', compact('item'));
    }

    public function download(PayrollItem $item)
    {
        $item->load('employee', 'period');

        if ($item->period->status === 'draft') {
            abort(403, 'Payslip is not available until the payroll period is approved.');
        }

        $pdf = Pdf::loadView('hr.payslip.pdf', compact('item'));

        $filename = 'payslip-' . $item->employee->employee_number . '-' . $item->period->period_name . '.pdf';

        return $pdf->download($filename);
    }
}