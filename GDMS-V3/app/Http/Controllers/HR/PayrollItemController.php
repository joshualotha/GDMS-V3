<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\PayrollItem;
use App\Services\PayrollService;
use Illuminate\Http\Request;

class PayrollItemController extends Controller
{
    protected $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    public function update(Request $request, PayrollItem $item)
    {
        $validated = $request->validate([
            'allowances' => 'nullable|numeric|min:0',
            'allowance_note' => 'nullable|string|max:255',
            'deductions' => 'nullable|numeric|min:0',
            'deduction_note' => 'nullable|string|max:255',
        ]);

        try {
            $this->payrollService->updatePayrollItem($item, $validated);
            return redirect()->back()
                ->with('success', 'Payroll item updated.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}