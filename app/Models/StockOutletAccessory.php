<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOutletAccessory extends Model
{
    protected $table = 'stock_outlet_accessories';

    protected $fillable = [
        'outlet_id',
        'accessory_id',
        'qty',
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
