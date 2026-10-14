<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'status',
        'type',
    ];

    /**
     * Assets assigned to this core department.
     */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    /**
     * Employees associated with this department.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Sub-departments, labs, and sections under this department.
     */
    public function subDepartments(): HasMany
    {
        return $this->hasMany(SubDepartment::class);
    }

    /**
     * Import records associated with this department.
     */
    public function imports(): HasMany
    {
        return $this->hasMany(Import::class);
    }

    /**
     * Occupancy history records where this was the previous department.
     */
    public function previousOccupancyHistories(): HasMany
    {
        return $this->hasMany(
            AssetOccupancyHistory::class,
            'previous_department_id'
        );
    }

    /**
     * Occupancy history records where this was the present department.
     */
    public function presentOccupancyHistories(): HasMany
    {
        return $this->hasMany(
            AssetOccupancyHistory::class,
            'present_department_id'
        );
    }
}
