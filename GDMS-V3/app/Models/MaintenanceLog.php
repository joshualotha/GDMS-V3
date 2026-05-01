<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceLog extends Model
{
    protected $fillable = [
        'asset_id',
        'date',
        'maintenance_type',
        'description',
        'cost',
        'vendor',
        'performed_by',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'date' => 'date',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(CompanyAsset::class, 'asset_id');
    }
}