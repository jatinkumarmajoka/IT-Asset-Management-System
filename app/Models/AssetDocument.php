<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetDocument extends Model
{
    use HasFactory;

    public const TYPE_ASSET_IMAGE = 'asset_image';

    public const TYPE_BILL_INVOICE = 'bill_invoice';

    protected $fillable = [
        'asset_id',
        'document_type',
        'original_name',
        'stored_path',
        'mime_type',
        'file_size',
        'uploaded_by',
        'locked_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'locked_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function isLocked(): bool
    {
        return $this->locked_at !== null;
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isBillInvoice(): bool
    {
        return $this->document_type === self::TYPE_BILL_INVOICE;
    }

    public function isAssetImage(): bool
    {
        return $this->document_type === self::TYPE_ASSET_IMAGE;
    }
}
