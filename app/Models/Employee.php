<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'employee_number',
        'first_name',
        'last_name',
        'id_number',
        'phone',
        'email',
        'role_title',
        'outlet_id',
        'hire_date',
        'basic_salary',
        'pay_type',
        'commission_rate',
        'commission_target',
        'status',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'hire_date' => 'date',
    ];

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function payrollItems(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(CompanyAsset::class, 'assigned_to_employee');
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Commission pay for a given month: rate x cylinders sold at this employee's
     * outlet once at/above target; below target the rate itself is scaled down by
     * how far short they fell (sold/target), so shortfalls cost more than a
     * straight-line proportional cut. The two formulas agree exactly at target.
     */
    public function calculateCommissionPay(int $month, int $year): array
    {
        $target = $this->commission_target ?: 1250;
        $rate = (float) ($this->commission_rate ?? 0);

        $cylindersSold = 0;
        if ($this->outlet_id) {
            $start = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
            $end = (clone $start)->endOfMonth();

            $cylindersSold = (int) SaleItem::whereHas('sale', function ($query) use ($start, $end) {
                $query->where('outlet_id', $this->outlet_id)
                    ->where('status', '!=', 'cancelled')
                    ->whereBetween('sale_date', [$start->toDateString(), $end->toDateString()]);
            })->sum('quantity');
        }

        $pay = $cylindersSold >= $target
            ? $rate * $cylindersSold
            : $rate * ($cylindersSold ** 2) / max($target, 1);

        return [
            'cylinders_sold' => $cylindersSold,
            'rate' => $rate,
            'target' => $target,
            'pay' => round($pay, 2),
        ];
    }
}