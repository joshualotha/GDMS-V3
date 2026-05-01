<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpeningStock extends Model
{
    protected $table = 'opening_stock';

    protected $fillable = [
        'reference',
        'outlet_id',
        'notes',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OpeningStockItem::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }
}