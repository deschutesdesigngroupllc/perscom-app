<?php

namespace App\Models;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;

/**
 * App\Models\Permission
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read bool $is_application_permission
 * @property-read bool $is_custom_permission
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 *
 * @method static \Database\Factories\PermissionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Permission extends \Spatie\Permission\Models\Permission implements Arrayable
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $casts = [
        'is_custom_permission' => 'boolean',
        'is_application_permission' => 'boolean',
    ];

    /**
     * @var string[]
     */
    protected $appends = ['is_custom_permission', 'is_application_permission'];

    public function getIsCustomPermissionAttribute(): bool
    {
        return ! self::getPermissionsFromConfig()->has($this->name);
    }

    public function getIsApplicationPermissionAttribute(): bool
    {
        return self::getPermissionsFromConfig()->has($this->name);
    }

    public static function getPermissionsFromConfig(): Collection
    {
        /** @var array<string, string> $permissions */
        $permissions = config('permissions.permissions');

        return collect($permissions);
    }
}
