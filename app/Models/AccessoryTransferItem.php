<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessoryTransferItem extends Model
{
    protected $fillable = [
        'accessory_transfer_id',
        'accessory_id',
        'quantity',
    ];

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(AccessoryTransfer::class, 'accessory_transfer_id');
    }

    public function accessory(): BelongsTo
    {
        return $this->belongsTo(Accessory::class);
    }
}
