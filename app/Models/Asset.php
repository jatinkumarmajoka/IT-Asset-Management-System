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

    /**
     * Attributes that can be mass assigned.
     */
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
        'asset_serial_number',
        'barcode_number',
        'date_of_issue',
        'asset_status',
        'condition',
        'location',
        'remarks',
        'created_by',
        'updated_by',
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'date_of_issue' => 'date',
        'issued_quantity' => 'integer',
        'physically_verified_quantity' => 'integer',
    ];

    /**
     * Department to which the asset belongs.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Category of the asset.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Current occupant of the asset.
     */
    public function occupant(): BelongsTo
    {
        return $this->belongsTo(
            Employee::class,
            'present_occupant_id'
        );
    }

    /**
     * Asset history records.
     */
    public function history(): HasMany
    {
        return $this->hasMany(AssetHistory::class);
    }
}