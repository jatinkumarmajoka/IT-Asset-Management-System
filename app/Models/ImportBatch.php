<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_filename',
        'stored_path',
        'file_type',
        'status',
        'total_rows',
        'valid_rows',
        'invalid_rows',
        'duplicate_rows',
        'imported_rows',
        'error_summary',
        'uploaded_by',
    ];

    protected $casts = [
        'error_summary' => 'array',
        'total_rows' => 'integer',
        'valid_rows' => 'integer',
        'invalid_rows' => 'integer',
        'duplicate_rows' => 'integer',
        'imported_rows' => 'integer',
    ];

    public function rows(): HasMany
    {
        return $this->hasMany(ImportRow::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'uploaded_by'
        );
    }
}
