<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsReceivedItem extends Model
{
    protected $table = 'goods_received_items';

    protected $fillable = [
        'goods_received_id',
        'cylinder_type_id',
        'purchase_type',
        'quantity',
        'unit_cost',
        'line_total',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function goodsReceived(): BelongsTo
    {
        return $this->belongsTo(GoodsReceived::class);
    }

    public function cylinderType(): BelongsTo
    {
        return $this->belongsTo(CylinderType::class);
    }
}