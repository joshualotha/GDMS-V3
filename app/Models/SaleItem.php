<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    protected $table = 'sale_items';

    protected $fillable = [
        'sale_id',
        'cylinder_type_id',
        'sale_type',
        'quantity',
        'unit_price',
        'unit_cost',
        'total_price',
        'total_cost',
        'gross_profit',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_price' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'gross_profit' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($item) {
            $item->total_price = $item->quantity * $item->unit_price;
            $item->total_cost = $item->quantity * $item->unit_cost;
            $item->gross_profit = $item->total_price - $item->total_cost;
        });
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function cylinderType(): BelongsTo
    {
        return $this->belongsTo(CylinderType::class);
    }
}