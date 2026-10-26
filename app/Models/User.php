<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
            'last_activity_at' => 'datetime',
        ];
    }

    /**
     * Department assigned to this login account.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin'
            && $this->account_status === 'approved';
    }

    public function isApproved(): bool
    {
        return $this->account_status === 'approved';
    }

    public function hasModuleAccess(string $module): bool
    {
        return $this->isApproved();
    }

    public function canManageModule(string $module): bool
    {
        if (! $this->isApproved()) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        $permissions = $this->permissions ?? [];

        if (! is_array($permissions)) {
            return false;
        }

        return filter_var(
            $permissions[$module] ?? false,
            FILTER_VALIDATE_BOOLEAN
        );
    }

    /**
     * Filter presets for this user.
     */
    public function filterPresets(): HasMany
    {
        return $this->hasMany(FilterPreset::class);
    }
}
