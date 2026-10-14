<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubDepartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'name',
        'code',
        'status',
        'description',
    ];

    /**
     * Core department that owns this sub-department.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Assets assigned to this sub-department.
     */
    public function assets(): HasMany
    {
        return $this->hasMany(
            Asset::class,
            'present_sub_department_id'
        );
    }

    /**
     * Occupancy history records where this was the previous sub-department.
     */
    public function previousOccupancyHistories(): HasMany
    {
        return $this->hasMany(
            AssetOccupancyHistory::class,
            'previous_sub_department_id'
        );
    }

    /**
     * Occupancy history records where this was the present sub-department.
     */
    public function presentOccupancyHistories(): HasMany
    {
        return $this->hasMany(
            AssetOccupancyHistory::class,
            'present_sub_department_id'
        );
    }
}
