<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashSubmission extends Model
{
    protected $table = 'cash_submissions';

    protected $fillable = [
        'reference',
        'sale_id',
        'expected_amount',
        'submitted_amount',
        'variance',
        'status',
        'receipt_image',
        'notes',
    ];

    protected $casts = [
        'expected_amount' => 'decimal:2',
        'submitted_amount' => 'decimal:2',
        'variance' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($submission) {
            $submission->variance = $submission->submitted_amount - $submission->expected_amount;
        });

        static::updating(function ($submission) {
            if ($submission->isDirty(['submitted_amount', 'expected_amount'])) {
                $submission->variance = $submission->submitted_amount - $submission->expected_amount;
            }
        });
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}