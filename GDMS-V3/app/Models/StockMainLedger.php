<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMainLedger extends Model
{
    protected $table = 'stock_main_ledger';

    protected $fillable = [
        'cylinder_type_id',
        'full_qty_change',
        'empty_qty_change',
        'full_qty_after',
        'empty_qty_after',
        'transaction_type',
        'reference_type',
        'reference_id',
        'note',
    ];

    public function cylinderType(): BelongsTo
    {
        return $this->belongsTo(CylinderType::class);
    }
}