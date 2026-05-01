<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmptyReturn extends Model
{
    protected $table = 'empty_returns';

    protected $fillable = [
        'return_number',
        'outlet_id',
        'status',
        'notes',
    ];

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(EmptyReturnItem::class);
    }
}