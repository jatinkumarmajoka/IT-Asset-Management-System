<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'asset_code',
        'department_id',
        'category_id',
        'item_name',
        'make',
        'model',
        'issued_quantity',
        'physically_verified_quantity',
        'present_occupant_id',
        'present_sub_department_id',
        'present_occupant_name',
        'asset_serial_number',
        'barcode_number',
        'date_of_issue',
        'asset_status',
        'condition',
        'location',
        'previous_department_name',
        'previous_location',
        'previous_room_number',
        'present_location',
        'present_room_number',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date_of_issue' => 'date',
        'issued_quantity' => 'integer',
        'physically_verified_quantity' => 'integer',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function occupant(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'present_occupant_id');
    }

    public function presentSubDepartment(): BelongsTo
    {
        return $this->belongsTo(
            SubDepartment::class,
            'present_sub_department_id'
        );
    }

    public function history(): HasMany
    {
        return $this->hasMany(AssetHistory::class);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(AssetTransfer::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(AssetDocument::class);
    }

    public function occupancyHistories(): HasMany
    {
        return $this->hasMany(AssetOccupancyHistory::class);
    }
}
