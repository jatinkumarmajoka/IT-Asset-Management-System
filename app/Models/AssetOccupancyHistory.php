<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetOccupancyHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',

        'previous_department_id',
        'previous_department_name',
        'previous_sub_department_id',
        'previous_sub_department_name',
        'previous_occupant_name',
        'previous_location',
        'previous_room_number',

        'present_department_id',
        'present_department_name',
        'present_sub_department_id',
        'present_sub_department_name',
        'present_occupant_name',
        'present_location',
        'present_room_number',

        'effective_at',
        'recorded_by',
    ];

    protected $casts = [
        'effective_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function previousDepartment(): BelongsTo
    {
        return $this->belongsTo(
            Department::class,
            'previous_department_id'
        );
    }

    public function previousSubDepartment(): BelongsTo
    {
        return $this->belongsTo(
            SubDepartment::class,
            'previous_sub_department_id'
        );
    }

    public function presentDepartment(): BelongsTo
    {
        return $this->belongsTo(
            Department::class,
            'present_department_id'
        );
    }

    public function presentSubDepartment(): BelongsTo
    {
        return $this->belongsTo(
            SubDepartment::class,
            'present_sub_department_id'
        );
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
