<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CylinderType extends Model
{
    protected $fillable = [
        'name',
        'size_kg',
        'full_sale_cost',
        'full_sale_price',
        'refill_cost',
        'refill_price',
        'is_active',
    ];

    protected $casts = [
        'full_sale_cost' => 'decimal:2',
        'full_sale_price' => 'decimal:2',
        'refill_cost' => 'decimal:2',
        'refill_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function priceHistory(): HasMany
    {
        return $this->hasMany(PriceHistory::class)->orderBy('effective_from', 'desc');
    }

    public function getFullSaleMarginAttribute(): float
    {
        if ($this->full_sale_cost > 0) {
            return (($this->full_sale_price - $this->full_sale_cost) / $this->full_sale_cost) * 100;
        }

        return 0;
    }

    public function getRefillMarginAttribute(): float
    {
        if ($this->refill_cost > 0) {
            return (($this->refill_price - $this->refill_cost) / $this->refill_cost) * 100;
        }

        return 0;
    }
}
