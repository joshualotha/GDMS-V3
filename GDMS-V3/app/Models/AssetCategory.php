<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_depreciable',
        'default_depreciation_rate',
        'useful_life_years',
        'is_active',
    ];

    protected $casts = [
        'is_depreciable' => 'boolean',
        'default_depreciation_rate' => 'decimal:2',
        'useful_life_years' => 'integer',
        'is_active' => 'boolean',
    ];

    public function assets(): HasMany
    {
        return $this->hasMany(CompanyAsset::class, 'asset_category_id');
    }
}