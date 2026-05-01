<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceHistory extends Model
{
    protected $table = 'price_history';

    protected $fillable = [
        'cylinder_type_id',
        'full_sale_cost',
        'full_sale_price',
        'refill_cost',
        'refill_price',
        'effective_from',
    ];

    protected $casts = [
        'full_sale_cost' => 'decimal:2',
        'full_sale_price' => 'decimal:2',
        'refill_cost' => 'decimal:2',
        'refill_price' => 'decimal:2',
        'effective_from' => 'datetime',
    ];

    public function cylinderType(): BelongsTo
    {
        return $this->belongsTo(CylinderType::class);
    }
}