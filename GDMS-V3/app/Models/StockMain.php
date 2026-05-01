<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMain extends Model
{
    protected $table = 'stock_main';

    protected $fillable = [
        'cylinder_type_id',
        'full_qty',
        'empty_qty',
    ];

    public function cylinderType(): BelongsTo
    {
        return $this->belongsTo(CylinderType::class);
    }
}