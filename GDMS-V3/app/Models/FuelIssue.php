<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuelIssue extends Model
{
    protected $fillable = [
        'date',
        'asset_id',
        'fuel_type',
        'litres',
        'odometer_km',
        'issued_by',
    ];

    protected $casts = [
        'date' => 'date',
        'litres' => 'decimal:2',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(FuelAsset::class);
    }
}
