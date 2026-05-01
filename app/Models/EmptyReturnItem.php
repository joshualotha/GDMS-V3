<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmptyReturnItem extends Model
{
    protected $table = 'empty_return_items';

    protected $fillable = [
        'empty_return_id',
        'cylinder_type_id',
        'quantity',
    ];

    public function emptyReturn(): BelongsTo
    {
        return $this->belongsTo(EmptyReturn::class);
    }

    public function cylinderType(): BelongsTo
    {
        return $this->belongsTo(CylinderType::class);
    }
}