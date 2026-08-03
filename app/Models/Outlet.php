<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Outlet extends Model
{
    protected $fillable = [
        'name',
        'type',
        'location',
        'plate_number',
        'asset_id',
        'is_active',
        'opened_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'opened_date' => 'date',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(CompanyAsset::class, 'asset_id');
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'outlet_id');
    }
}
