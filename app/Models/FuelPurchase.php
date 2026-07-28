<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuelPurchase extends Model
{
    protected $fillable = [
        'date',
        'fuel_type',
        'litres',
        'unit_cost',
        'total_cost',
        'supplier',
        'supplier_id',
        'receipt_number',
    ];

    protected $casts = [
        'date' => 'date',
        'litres' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($purchase) {
            $purchase->total_cost = $purchase->litres * $purchase->unit_cost;
        });
    }

    public function supplierAccount(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
