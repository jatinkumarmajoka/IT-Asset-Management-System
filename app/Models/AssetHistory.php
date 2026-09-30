<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'asset_id', 'action', 'old_value', 'new_value',
        'performed_by', 'ip_address', 'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
