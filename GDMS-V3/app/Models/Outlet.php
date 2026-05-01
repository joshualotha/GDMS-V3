<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    protected $fillable = [
        'name',
        'type',
        'location',
        'plate_number',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}