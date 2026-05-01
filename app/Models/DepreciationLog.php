<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepreciationLog extends Model
{
    protected $fillable = [
        'asset_id',
        'period_start',
        'period_end',
        'book_value_before',
        'depreciation_rate',
        'depreciation_amount',
        'book_value_after',
        'run_by',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'book_value_before' => 'decimal:2',
        'depreciation_amount' => 'decimal:2',
        'book_value_after' => 'decimal:2',
        'depreciation_rate' => 'decimal:2',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(CompanyAsset::class, 'asset_id');
    }
}