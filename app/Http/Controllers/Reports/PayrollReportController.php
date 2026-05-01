<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\PayrollPeriod;
use App\Models\PayrollItem;
use App\Models\Employee;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PayrollReportController extends Controller
{
    public function index(Request $request)
    {
        $periods = PayrollPeriod::whereIn('status', ['approved', 'paid'])
            ->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->get();

        $selectedPeriod = null;
        $items = collect();
        $selectedEmployee = null;
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();

        $periodId = $request->period_id;
        
        if (!$periodId && $periods->isNotEmpty()) {
            $periodId = $periods->first()->id;
        }

        if ($periodId) {
            $selectedPeriod = PayrollPeriod::find($periodId);
            if ($selectedPeriod) {
                $query = $selectedPeriod->items()->with('employee');
                
                if ($request->employee_id) {
                    $query->where('employee_id', $request->employee_id);
                    $selectedEmployee = Employee::find($request->employee_id);
                }
                
                $items = $query->get();
            }
        }

        return view('reports.payroll.index', compact(
            'periods', 'selectedPeriod', 'items', 'employees', 'selectedEmployee'
        ));
    }

    public function export(Request $request)
    {
        $period = PayrollPeriod::find($request->period_id);
        
        if (!$period) {
            return back()->with('error', 'Period not found');
        }

        if (!in_array($period->status, ['approved', 'paid'])) {
            return back()->with('error', 'Only approved or paid periods can be exported.');
        }

        $items = $period->items()->with('employee')->get();

        $pdf = Pdf::loadView('reports.payroll.pdf', compact(
            'period', 'items'
        ));

        return $pdf->download('payroll-report-' . $period->period_name . '.pdf');
    }
}