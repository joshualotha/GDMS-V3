<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StockAdjustment extends Model
{
    protected $table = 'stock_adjustments';

    protected $fillable = [
        'adjustment_number',
        'reverses_adjustment_id',
        'payroll_item_id',
        'outlet_id',
        'adjustment_date',
        'is_main',
        'cylinder_type_id',
        'type',
        'full_qty_change',
        'empty_qty_change',
        'reason',
    ];

    protected $casts = [
        'adjustment_date' => 'date',
    ];

    public function cylinderType(): BelongsTo
    {
        return $this->belongsTo(CylinderType::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function reverses(): BelongsTo
    {
        return $this->belongsTo(StockAdjustment::class, 'reverses_adjustment_id');
    }

    public function reversal(): HasOne
    {
        return $this->hasOne(StockAdjustment::class, 'reverses_adjustment_id');
    }

    public function payrollItem(): BelongsTo
    {
        return $this->belongsTo(PayrollItem::class);
    }

    /**
     * Whether this is a cylinder loss at an outlet — the only case that docks an
     * employee's pay (main-warehouse losses have no single responsible employee).
     */
    public function isOutletLoss(): bool
    {
        return $this->type === 'loss' && ! $this->is_main && $this->outlet_id !== null;
    }

    /**
     * Cost-price value of the lost cylinders (container + gas), used as the payroll
     * deduction. Uses replacement cost, not retail price.
     */
    public function lossDeductionAmount(): float
    {
        $qtyLost = $this->full_qty_change + $this->empty_qty_change;

        return round($qtyLost * (float) ($this->cylinderType->full_sale_cost ?? 0), 2);
    }

    /**
     * The actual signed stock change this adjustment applied (loss flips the entered magnitude negative).
     */
    public function appliedFullChange(): int
    {
        return $this->type === 'loss' ? -$this->full_qty_change : $this->full_qty_change;
    }

    public function appliedEmptyChange(): int
    {
        return $this->type === 'loss' ? -$this->empty_qty_change : $this->empty_qty_change;
    }
}
