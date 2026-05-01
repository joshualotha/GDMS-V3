<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\PayrollPeriod;
use App\Services\PayrollService;
use Illuminate\Http\Request;

class PayrollPeriodController extends Controller
{
    protected $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    public function index()
    {
        $periods = PayrollPeriod::orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->get();
        return view('hr.payroll.index', compact('periods'));
    }

    public function create()
    {
        return view('hr.payroll.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2000|max:2100',
        ]);

        try {
            $period = $this->payrollService->generatePeriod(
                $validated['period_month'],
                $validated['period_year']
            );
            return redirect()->route('payroll.show', $period)
                ->with('success', 'Payroll period generated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    public function show(PayrollPeriod $period)
    {
        $period->load('items.employee');
        return view('hr.payroll.show', compact('period'));
    }

    public function approve(Request $request, PayrollPeriod $period)
    {
        try {
            $this->payrollService->approvePeriod($period, auth()->id());
            return redirect()->back()
                ->with('success', 'Payroll period approved.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    public function markPaid(Request $request, PayrollPeriod $period)
    {
        try {
            $this->payrollService->markAsPaid($period);
            return redirect()->back()
                ->with('success', 'Payroll period marked as paid.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}