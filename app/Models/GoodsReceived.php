<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoodsReceived extends Model
{
    protected $table = 'goods_received';

    protected $fillable = [
        'grn_number',
        'supplier_id',
        'purchase_order_id',
        'status',
        'total_cost',
        'notes',
    ];

    protected $casts = [
        'total_cost' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(GoodsReceivedItem::class);
    }
}