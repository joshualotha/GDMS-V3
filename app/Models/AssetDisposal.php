<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetDisposal extends Model
{
    protected $fillable = [
        'asset_id',
        'disposal_date',
        'disposal_method',
        'proceeds',
        'loss',
        'approved_by',
        'notes',
    ];

    protected $casts = [
        'disposal_date' => 'date',
        'proceeds' => 'decimal:2',
        'loss' => 'decimal:2',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(CompanyAsset::class, 'asset_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}