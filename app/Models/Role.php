<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Role
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read bool $is_application_role
 * @property-read bool $is_custom_role
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 *
 * @method static \Database\Factories\RoleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Role extends \Spatie\Permission\Models\Role
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $casts = [
        'is_custom_role' => 'boolean',
        'is_application_role' => 'boolean',
    ];

    /**
     * @var string[]
     */
    protected $appends = ['is_custom_role', 'is_application_role'];

    public function getIsCustomRoleAttribute(): bool
    {
        /** @var array<string, string> $roles */
        $roles = config('permissions.roles');

        return ! collect($roles)->has($this->name);
    }

    public function getIsApplicationRoleAttribute(): bool
    {
        /** @var array<string, string> $roles */
        $roles = config('permissions.roles');

        return collect($roles)->has($this->name);
    }
}
