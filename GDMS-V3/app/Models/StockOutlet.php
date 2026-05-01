<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOutlet extends Model
{
    protected $table = 'stock_outlet';

    protected $fillable = [
        'outlet_id',
        'cylinder_type_id',
        'full_qty',
        'empty_qty',
    ];

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function cylinderType(): BelongsTo
    {
        return $this->belongsTo(CylinderType::class);
    }
}