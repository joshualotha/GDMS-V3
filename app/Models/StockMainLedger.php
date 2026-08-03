<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMainLedger extends Model
{
    protected $table = 'stock_main_ledger';

    protected $fillable = [
        'cylinder_type_id',
        'outlet_id',
        'movement_date',
        'full_qty_change',
        'empty_qty_change',
        'full_qty_after',
        'empty_qty_after',
        'transaction_type',
        'reference_type',
        'reference_id',
        'note',
    ];

    protected $casts = [
        'movement_date' => 'date',
    ];

    public function cylinderType(): BelongsTo
    {
        return $this->belongsTo(CylinderType::class);
    }
}