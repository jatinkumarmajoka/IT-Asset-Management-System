<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
     * Check whether the user is an approved Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin'
            && $this->account_status === 'approved';
    }

    /**
     * Check whether the user's account is approved.
     */
    public function isApproved(): bool
    {
        return $this->account_status === 'approved';
    }

    /**
     * Check whether the user can access a module.
     * Approved staff can view modules.
     */
    public function hasModuleAccess(string $module): bool
    {
        return $this->isApproved();
    }

    /**
     * Check whether the user can create, edit, or delete
     * records in a specific module.
     */
    public function canManageModule(string $module): bool
    {
        if (!$this->isApproved()) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        $permissions = $this->permissions ?? [];

        if (!is_array($permissions)) {
            return false;
        }

        return filter_var(
            $permissions[$module] ?? false,
            FILTER_VALIDATE_BOOLEAN
        );
    }
}
