<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetTransferDocument extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Document Types
    |--------------------------------------------------------------------------
    */

    public const TYPE_TRANSFER_IMAGE = 'transfer_image';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'asset_transfer_id',
        'document_type',
        'original_name',
        'stored_path',
        'mime_type',
        'file_size',
        'uploaded_by',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'file_size' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Transfer
    |--------------------------------------------------------------------------
    */

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(
            AssetTransfer::class,
            'asset_transfer_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Uploaded By
    |--------------------------------------------------------------------------
    */

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'uploaded_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isTransferImage(): bool
    {
        return $this->document_type === self::TYPE_TRANSFER_IMAGE;
    }

    public function isImage(): bool
    {
        return str_starts_with(
            $this->mime_type,
            'image/'
        );
    }
}