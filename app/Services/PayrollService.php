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

    public function unapprovePeriod(PayrollPeriod $period): PayrollPeriod
    {
        if ($period->status !== 'approved') {
            throw new \Exception("Only approved periods can be reverted to draft.");
        }

        $period->update([
            'status' => 'draft',
            'approved_by' => null,
            'approved_at' => null,
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

    public function addEmployeeToPeriod(PayrollPeriod $period, Employee $employee): PayrollItem
    {
        if ($period->status !== 'draft') {
            throw new \Exception("Employees can only be added to a draft payroll period.");
        }

        if ($period->items()->where('employee_id', $employee->id)->exists()) {
            throw new \Exception("{$employee->full_name} is already in this payroll period.");
        }

        $item = PayrollItem::create([
            'payroll_period_id' => $period->id,
            'employee_id' => $employee->id,
            'basic_salary' => $employee->basic_salary,
            'allowances' => 0,
            'deductions' => 0,
            'net_pay' => $employee->basic_salary,
        ]);

        $this->recalculatePeriodTotals($period);

        return $item;
    }

    public function updatePayrollItem(PayrollItem $item, array $data): PayrollItem
    {
        if ($item->period->status !== 'draft') {
            throw new \Exception("Cannot edit items in a non-draft period.");
        }

        $allowances = $data['allowances'] ?? 0;
        $deductions = $data['deductions'] ?? 0;

        if (round((float) $item->basic_salary + $allowances - $deductions, 2) < 0) {
            throw new \Exception("Deductions cannot exceed basic salary plus allowances.");
        }

        $item->update([
            'allowances' => $allowances,
            'allowance_note' => $data['allowance_note'] ?? null,
            'deductions' => $deductions,
            'deduction_note' => $data['deduction_note'] ?? null,
        ]);

        $this->recalculatePeriodTotals($item->period);

        return $item;
    }
}