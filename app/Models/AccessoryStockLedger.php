<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessoryStockLedger extends Model
{
    protected $table = 'accessory_stock_ledger';

    protected $fillable = [
        'accessory_id',
        'outlet_id',
        'movement_date',
        'qty_change',
        'qty_after',
        'transaction_type',
        'reference_type',
        'reference_id',
        'note',
    ];

    protected $casts = [
        'movement_date' => 'date',
    ];

    public function accessory(): BelongsTo
    {
        return $this->belongsTo(Accessory::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }
}
