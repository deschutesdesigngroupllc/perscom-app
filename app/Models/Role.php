<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ClearsResponseCache;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $guard_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read bool $is_application_role
 * @property-read bool $is_custom_role
 * @property-read Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 *
 * @method static \Database\Factories\RoleFactory factory($count = null, $state = [])
 * @method static Builder<static>|Role newModelQuery()
 * @method static Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role permission($permissions, $without = false)
 * @method static Builder<static>|Role query()
 * @method static Builder<static>|Role whereCreatedAt($value)
 * @method static Builder<static>|Role whereDescription($value)
 * @method static Builder<static>|Role whereGuardName($value)
 * @method static Builder<static>|Role whereId($value)
 * @method static Builder<static>|Role whereName($value)
 * @method static Builder<static>|Role whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role withoutPermission($permissions)
 *
 * @mixin \Eloquent
 */
class Role extends \Spatie\Permission\Models\Role
{
    use ClearsResponseCache;
    use HasFactory;

    protected $appends = [
        'is_custom_role',
        'is_application_role',
    ];

    /**
     * @return Attribute<bool, never>
     */
    public function isCustomRole(): Attribute
    {
        return Attribute::get(fn (): bool => ! $this->is_application_role)->shouldCache();
    }

    /**
     * @return Attribute<bool, never>
     */
    public function isApplicationRole(): Attribute
    {
        return Attribute::get(fn ($value, $attributes = null): bool => data_get($attributes, 'name') === Utils::getSuperAdminName())->shouldCache();
    }

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'is_custom_role' => 'boolean',
            'is_application_role' => 'boolean',
        ];
    }
}
