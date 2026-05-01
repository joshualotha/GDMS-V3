<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FuelAsset extends Model
{
    protected $table = 'fuel_assets';

    protected $fillable = [
        'name',
        'type',
        'plate_number',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function fuelIssues(): HasMany
    {
        return $this->hasMany(FuelIssue::class);
    }
}