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
        return view('hr.payslip.show', compact('item'));
    }

    public function download(PayrollItem $item)
    {
        $item->load('employee', 'period');

        $pdf = Pdf::loadView('hr.payslip.pdf', compact('item'));

        $filename = 'payslip-' . $item->employee->employee_number . '-' . $item->period->period_name . '.pdf';

        return $pdf->download($filename);
    }
}