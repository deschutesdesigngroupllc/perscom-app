<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends \Spatie\Permission\Models\Role
{
    use HasFactory;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_custom_role' => 'boolean',
        'is_application_role' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['is_custom_role', 'is_application_role'];

    /**
     * @return bool
     */
    public function getIsCustomRoleAttribute()
    {
        return !collect(config('permissions.roles'))->has($this->name);
    }

    /**
     * @return bool
     */
    public function getIsApplicationRoleAttribute()
    {
        return collect(config('permissions.roles'))->has($this->name);
    }
}
