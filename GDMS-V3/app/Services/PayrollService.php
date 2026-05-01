<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\PayrollItem;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    public function generatePeriod(int $month, int $year): PayrollPeriod
    {
        return DB::transaction(function () use ($month, $year) {
            $existing = PayrollPeriod::where('period_month', $month)
                ->where('period_year', $year)
                ->first();

            if ($existing) {
                throw new \Exception("Payroll period for {$month}/{$year} already exists.");
            }

            $period = PayrollPeriod::create([
                'period_month' => $month,
                'period_year' => $year,
                'status' => 'draft',
            ]);

            $employees = Employee::where('status', 'active')->get();

            foreach ($employees as $employee) {
                PayrollItem::create([
                    'payroll_period_id' => $period->id,
                    'employee_id' => $employee->id,
                    'basic_salary' => $employee->basic_salary,
                    'allowances' => 0,
                    'deductions' => 0,
                    'net_pay' => $employee->basic_salary,
                ]);
            }

            $this->recalculatePeriodTotals($period);

            return $period;
        });
    }

    public function recalculatePeriodTotals(PayrollPeriod $period): void
    {
        $items = $period->items()->get();

        $totalGross = $items->sum('basic_salary') + $items->sum('allowances');
        $totalDeductions = $items->sum('deductions');
        $totalNet = $items->sum('net_pay');

        $period->update([
            'total_gross' => $totalGross,
            'total_deductions' => $totalDeductions,
            'total_net' => $totalNet,
        ]);
    }

    public function approvePeriod(PayrollPeriod $period, int $userId): PayrollPeriod
    {
        if ($period->status !== 'draft') {
            throw new \Exception("Only draft periods can be approved.");
        }

        $period->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);

        return $period;
    }

    public function markAsPaid(PayrollPeriod $period): PayrollPeriod
    {
        if ($period->status !== 'approved') {
            throw new \Exception("Only approved periods can be marked as paid.");
        }

        $period->update([
            'status' => 'paid',
        ]);

        return $period;
    }

    public function updatePayrollItem(PayrollItem $item, array $data): PayrollItem
    {
        if ($item->period->status !== 'draft') {
            throw new \Exception("Cannot edit items in a non-draft period.");
        }

        $item->update([
            'allowances' => $data['allowances'] ?? $item->allowances,
            'allowance_note' => $data['allowance_note'] ?? $item->allowance_note,
            'deductions' => $data['deductions'] ?? $item->deductions,
            'deduction_note' => $data['deduction_note'] ?? $item->deduction_note,
        ]);

        $this->recalculatePeriodTotals($item->period);

        return $item;
    }
}