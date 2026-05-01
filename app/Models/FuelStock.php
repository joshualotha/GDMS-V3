<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelStock extends Model
{
    protected $table = 'fuel_stock';

    protected $fillable = [
        'fuel_type',
        'litres',
    ];

    protected $casts = [
        'litres' => 'decimal:2',
    ];
}