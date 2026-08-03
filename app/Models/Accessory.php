<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accessory extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'cost_price',
        'sale_price',
        'is_active',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function getMarginAttribute(): float
    {
        if ($this->cost_price > 0) {
            return (($this->sale_price - $this->cost_price) / $this->cost_price) * 100;
        }

        return 0;
    }
}
