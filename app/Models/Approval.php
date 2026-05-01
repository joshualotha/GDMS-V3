<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Approval extends Model
{
    protected $table = 'approvals';

    protected $fillable = [
        'approvable_type',
        'approvable_id',
        'action',
        'note',
    ];

    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }
}