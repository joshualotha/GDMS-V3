<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMainAccessory extends Model
{
    protected $table = 'stock_main_accessories';

    protected $fillable = [
        'accessory_id',
        'qty',
    ];

    public function accessory(): BelongsTo
    {
        return $this->belongsTo(Accessory::class);
    }
}
