<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AssetTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'from_department_id',
        'to_department_id',
        'from_occupant_id',
        'to_occupant_id',
        'transfer_date',
        'received_date',
        'status',
        'reason',
        'remarks',
        'reference_number',
        'initiated_by',
        'approved_by',
        'completed_by',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'received_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Asset
    |--------------------------------------------------------------------------
    */

    public function asset(): BelongsTo
    {
        return $this->belongsTo(
            Asset::class,
            'asset_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | From Department
    |--------------------------------------------------------------------------
    */

    public function fromDepartment(): BelongsTo
    {
        return $this->belongsTo(
            Department::class,
            'from_department_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | To Department
    |--------------------------------------------------------------------------
    */

    public function toDepartment(): BelongsTo
    {
        return $this->belongsTo(
            Department::class,
            'to_department_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | From Occupant
    |--------------------------------------------------------------------------
    */

    public function fromOccupant(): BelongsTo
    {
        return $this->belongsTo(
            Employee::class,
            'from_occupant_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | To Occupant
    |--------------------------------------------------------------------------
    */

    public function toOccupant(): BelongsTo
    {
        return $this->belongsTo(
            Employee::class,
            'to_occupant_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Initiated By
    |--------------------------------------------------------------------------
    */

    public function initiatedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'initiated_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Approved By
    |--------------------------------------------------------------------------
    */

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'approved_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Completed By
    |--------------------------------------------------------------------------
    */

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'completed_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Transfer Documents
    |--------------------------------------------------------------------------
    */

    public function documents(): HasMany
    {
        return $this->hasMany(
            AssetTransferDocument::class,
            'asset_transfer_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Transfer Image
    |--------------------------------------------------------------------------
    */

    public function transferImage(): HasOne
    {
        return $this->hasOne(
            AssetTransferDocument::class,
            'asset_transfer_id'
        )->where(
            'document_type',
            AssetTransferDocument::TYPE_TRANSFER_IMAGE
        );
    }
}