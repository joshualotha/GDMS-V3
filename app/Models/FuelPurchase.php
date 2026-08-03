<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuelPurchase extends Model
{
    protected $fillable = [
        'date',
        'outlet_id',
        'odometer_km',
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
            // Pump price varies per fill-up: the pump gives litres for a fixed cash amount
            // (total_cost), so unit cost is derived from that rather than entered directly.
            if ($purchase->outlet_id && $purchase->litres > 0) {
                $purchase->unit_cost = round($purchase->total_cost / $purchase->litres, 2);
            } else {
                $purchase->total_cost = $purchase->litres * $purchase->unit_cost;
            }
        });
    }

    public function supplierAccount(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }
}
