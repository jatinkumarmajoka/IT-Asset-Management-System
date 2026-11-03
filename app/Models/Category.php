<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'status', 'parent_id'];

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function isParent(): bool
    {
        return $this->parent_id === null;
    }

    public function getTopParent(): self
    {
        if ($this->parent_id === null) {
            return $this;
        }

        return $this->parent()->with('parent')->first()->getTopParent();
    }

    public function getTopParentName(): string
    {
        return $this->getTopParent()->name;
    }
}
