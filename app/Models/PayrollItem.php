<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollItem extends Model
{
    protected $table = 'payroll_items';

    protected $fillable = [
        'payroll_period_id',
        'employee_id',
        'basic_salary',
        'allowances',
        'allowance_note',
        'deductions',
        'deduction_note',
        'net_pay',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'allowances' => 'decimal:2',
        'deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    protected static function booted(): void
    {
        static::creating(function (PayrollItem $item) {
            $item->net_pay = $item->basic_salary + $item->allowances - $item->deductions;
        });

        static::updating(function (PayrollItem $item) {
            $item->net_pay = $item->basic_salary + $item->allowances - $item->deductions;
        });
    }
}