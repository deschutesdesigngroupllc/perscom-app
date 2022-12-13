<?php

namespace App\Models;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends \Spatie\Permission\Models\Permission implements Arrayable
{
    use HasFactory;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_custom_permission' => 'boolean',
        'is_application_permission' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'is_custom_permission',
        'is_application_permission',
    ];

    /**
     * @return bool
     */
    public function getIsCustomPermissionAttribute()
    {
        return ! self::getPermissionsFromConfig()->has($this->name);
    }

    /**
     * @return bool
     */
    public function getIsApplicationPermissionAttribute()
    {
        return self::getPermissionsFromConfig()->has($this->name);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public static function getPermissionsFromConfig()
    {
        return collect(config('permissions.permissions'));
    }
}
