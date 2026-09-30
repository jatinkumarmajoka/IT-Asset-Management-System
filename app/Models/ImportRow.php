<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportRow extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_batch_id',
        'row_number',
        'raw_data',
        'normalized_data',
        'status',
        'errors',
        'duplicate_of_asset_id',
    ];

    protected $casts = [
        'raw_data' => 'array',
        'normalized_data' => 'array',
        'errors' => 'array',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(
            ImportBatch::class,
            'import_batch_id'
        );
    }

    public function duplicateAsset(): BelongsTo
    {
        return $this->belongsTo(
            Asset::class,
            'duplicate_of_asset_id'
        );
    }
}