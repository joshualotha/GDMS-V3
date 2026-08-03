<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccessoryPurchase extends Model
{
    protected $fillable = [
        'purchase_number',
        'purchase_date',
        'supplier_id',
        'total_cost',
        'receipt_number',
        'notes',
        'status',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_cost' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(AccessoryPurchaseItem::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
