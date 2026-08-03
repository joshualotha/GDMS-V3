<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\PayrollItem;
use App\Models\StockAdjustment;
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
                [$data, $pendingLossIds] = $this->buildPayrollItemData($period, $employee);
                $item = PayrollItem::create($data);
                $this->markLossesApplied($pendingLossIds, $item);
            }

            $this->recalculatePeriodTotals($period);

            return $period;
        });
    }

    /**
     * Salary employees carry their basic_salary as-is. Commission employees have
     * their pay computed now from that month's sales and snapshotted onto the item,
     * so later rate changes don't retroactively rewrite an already-generated payslip.
     * Any outlet cylinder losses not yet absorbed into a payroll item (because no
     * draft period existed for this employee when they were recorded) are swept in here.
     */
    protected function buildPayrollItemData(PayrollPeriod $period, Employee $employee): array
    {
        $data = [
            'payroll_period_id' => $period->id,
            'employee_id' => $employee->id,
            'allowances' => 0,
            'deductions' => 0,
        ];

        if ($employee->pay_type === 'commission') {
            $commission = $employee->calculateCommissionPay($period->period_month, $period->period_year);

            $data['basic_salary'] = $commission['pay'];
            $data['cylinders_sold'] = $commission['cylinders_sold'];
            $data['commission_rate'] = $commission['rate'];
            $data['commission_target'] = $commission['target'];
        } else {
            $data['basic_salary'] = $employee->basic_salary;
        }

        [$lossDeduction, $lossNote, $pendingLossIds] = $this->pendingOutletLosses($employee);
        $data['loss_deductions'] = $lossDeduction;
        $data['loss_deduction_note'] = $lossNote;

        $data['net_pay'] = $data['basic_salary'] - $lossDeduction;

        return [$data, $pendingLossIds];
    }

    /**
     * Sum up any outlet cylinder-loss deductions for this employee that haven't yet
     * been absorbed into a payroll item, and the adjustment IDs to mark once applied.
     */
    protected function pendingOutletLosses(Employee $employee): array
    {
        if (! $employee->outlet_id) {
            return [0, null, []];
        }

        $losses = StockAdjustment::where('type', 'loss')
            ->where('is_main', false)
            ->where('outlet_id', $employee->outlet_id)
            ->whereNull('payroll_item_id')
            ->with('cylinderType')
            ->get();

        if ($losses->isEmpty()) {
            return [0, null, []];
        }

        $total = round($losses->sum(fn (StockAdjustment $loss) => $loss->lossDeductionAmount()), 2);
        $note = 'Cylinder loss: '.$losses->pluck('adjustment_number')->implode(', ');

        return [$total, $note, $losses->pluck('id')->all()];
    }

    protected function markLossesApplied(array $adjustmentIds, PayrollItem $item): void
    {
        if (! empty($adjustmentIds)) {
            StockAdjustment::whereIn('id', $adjustmentIds)->update(['payroll_item_id' => $item->id]);
        }
    }

    /**
     * Called right when an outlet cylinder-loss adjustment is posted. If the responsible
     * employee already has a draft payroll item open, dock it immediately; otherwise the
     * loss stays pending and gets swept in automatically next time a period is generated
     * for them (see buildPayrollItemData/pendingOutletLosses above).
     */
    public function applyOutletLossToPayroll(StockAdjustment $adjustment): void
    {
        if (! $adjustment->isOutletLoss() || $adjustment->payroll_item_id !== null) {
            return;
        }

        $employee = $adjustment->outlet->employee ?? null;
        if (! $employee) {
            return;
        }

        $item = PayrollItem::where('employee_id', $employee->id)
            ->whereHas('period', fn ($q) => $q->where('status', 'draft'))
            ->latest('id')
            ->first();

        if (! $item) {
            return;
        }

        $amount = $adjustment->lossDeductionAmount();
        $note = trim(($item->loss_deduction_note ? $item->loss_deduction_note.'; ' : '').'Cylinder loss: '.$adjustment->adjustment_number);

        $item->update([
            'loss_deductions' => $item->loss_deductions + $amount,
            'loss_deduction_note' => $note,
        ]);

        $adjustment->update(['payroll_item_id' => $item->id]);

        $this->recalculatePeriodTotals($item->period);
    }

    /**
     * Refund a loss deduction when its adjustment is reversed — only possible if the
     * payroll item it was applied to is still in draft (locked periods are left alone;
     * the caller should surface that a manual correction is needed).
     */
    public function refundReversedLoss(StockAdjustment $originalLoss): bool
    {
        if (! $originalLoss->payroll_item_id) {
            return true; // never applied, nothing to refund
        }

        $item = $originalLoss->payrollItem;
        if (! $item || $item->period->status !== 'draft') {
            return false;
        }

        $amount = $originalLoss->lossDeductionAmount();
        $newLossDeductions = max(0, $item->loss_deductions - $amount);

        $item->update([
            'loss_deductions' => $newLossDeductions,
            'loss_deduction_note' => trim(($item->loss_deduction_note ?? '').'; Refunded: reversal of '.$originalLoss->adjustment_number),
        ]);

        $this->recalculatePeriodTotals($item->period);

        return true;
    }

    public function recalculatePeriodTotals(PayrollPeriod $period): void
    {
        $items = $period->items()->get();

        $totalGross = $items->sum('basic_salary') + $items->sum('allowances');
        $totalDeductions = $items->sum('deductions') + $items->sum('loss_deductions');
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

    public function markAsPaid(PayrollPeriod $period, ?string $paidAt = null): PayrollPeriod
    {
        if ($period->status !== 'approved') {
            throw new \Exception("Only approved periods can be marked as paid.");
        }

        $period->update([
            'status' => 'paid',
            'paid_at' => $paidAt ?? now(),
        ]);

        return $period;
    }

    public function deletePeriod(PayrollPeriod $period): void
    {
        if ($period->status !== 'draft') {
            throw new \Exception("Only draft payroll periods can be deleted. Revert to draft first if needed.");
        }

        DB::transaction(function () use ($period) {
            $period->items()->delete();
            $period->delete();
        });
    }

    public function removeEmployeeFromPeriod(PayrollPeriod $period, PayrollItem $item): void
    {
        if ($period->status !== 'draft') {
            throw new \Exception("Employees can only be removed from a draft payroll period.");
        }

        if ($item->payroll_period_id !== $period->id) {
            throw new \Exception("This payroll item does not belong to this period.");
        }

        $item->delete();

        $this->recalculatePeriodTotals($period);
    }

    public function addEmployeeToPeriod(PayrollPeriod $period, Employee $employee): PayrollItem
    {
        if ($period->status !== 'draft') {
            throw new \Exception("Employees can only be added to a draft payroll period.");
        }

        if ($period->items()->where('employee_id', $employee->id)->exists()) {
            throw new \Exception("{$employee->full_name} is already in this payroll period.");
        }

        [$data, $pendingLossIds] = $this->buildPayrollItemData($period, $employee);
        $item = PayrollItem::create($data);
        $this->markLossesApplied($pendingLossIds, $item);

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

        if (round((float) $item->basic_salary + $allowances - $deductions - $item->loss_deductions, 2) < 0) {
            throw new \Exception("Deductions cannot exceed basic salary plus allowances (after cylinder-loss deductions).");
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