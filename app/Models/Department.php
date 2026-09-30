<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'status',
    ];

    /**
     * Assets belonging to this department.
     */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    /**
     * Employees belonging to this department.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}