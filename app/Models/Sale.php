<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $table = 'sales';

    protected $fillable = [
        'sale_number',
        'outlet_id',
        'sale_date',
        'status',
        'total_price',
        'total_cost',
        'total_gross_profit',
        'notes',
        'cash_submitted',
        'cash_submitted_date',
        'cash_submitted_by',
        'cash_variance',
        'cash_receipt_image',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'sale_date' => 'date',
        'total_price' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'total_gross_profit' => 'decimal:2',
        'cash_submitted' => 'decimal:2',
        'cash_submitted_date' => 'date',
        'cash_variance' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::saving(function ($sale) {
            if ($sale->cash_submitted !== null && $sale->cash_submitted_date !== null) {
                $sale->cash_variance = floatval($sale->cash_submitted) - floatval($sale->total_price);
            }
        });
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }
}
