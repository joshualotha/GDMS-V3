<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CompanyAsset extends Model
{
    protected $table = 'assets';

    protected $fillable = [
        'asset_number',
        'name',
        'asset_category_id',
        'serial_number',
        'plate_number',
        'purchase_date',
        'purchase_cost',
        'accumulated_depreciation',
        'current_book_value',
        'depreciation_rate',
        'assigned_to_outlet',
        'assigned_to_employee',
        'status',
        'disposed_at',
        'disposal_notes',
    ];

    protected $casts = [
        'purchase_cost' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'current_book_value' => 'decimal:2',
        'depreciation_rate' => 'decimal:2',
        'purchase_date' => 'date',
        'disposed_at' => 'date',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class, 'assigned_to_outlet');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to_employee');
    }

    public function depreciationLogs(): HasMany
    {
        return $this->hasMany(DepreciationLog::class, 'asset_id');
    }

    /**
     * Maintenance costs are recorded as regular Expenses tagged with this asset,
     * not a separate maintenance log.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'asset_id');
    }

    /**
     * If this asset IS a vehicle used as a car-outlet, this is that outlet.
     */
    public function outletAsCar(): HasOne
    {
        return $this->hasOne(Outlet::class, 'asset_id');
    }
}