<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuelIssue extends Model
{
    protected $fillable = [
        'date',
        'outlet_id',
        'fuel_type',
        'litres',
        'odometer_km',
        'issued_by',
    ];

    protected $casts = [
        'date' => 'date',
        'litres' => 'decimal:2',
    ];

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }
}
