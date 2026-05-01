<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelPurchase extends Model
{
    protected $fillable = [
        'date',
        'fuel_type',
        'litres',
        'unit_cost',
        'total_cost',
        'supplier',
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
}
