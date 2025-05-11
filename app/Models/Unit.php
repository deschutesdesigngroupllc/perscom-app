<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Hideable;
use App\Models\Enums\AssignmentRecordType;
use App\Models\Scopes\UnitScope;
use App\Traits\CanBeHidden;
use App\Traits\CanBeOrdered;
use App\Traits\CanReceiveNotifications;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasAssignmentRecords;
use App\Traits\HasIcon;
use App\Traits\HasImages;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use App\Traits\HasUsers;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\EloquentSortable\Sortable;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $empty
 * @property int $order
 * @property bool $hidden
 * @property string|null $icon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AssignmentRecord> $assignment_records
 * @property-read int|null $assignment_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Group> $groups
 * @property-read int|null $groups_count
 * @property-read Image|null $image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Image> $images
 * @property-read int|null $images_count
 * @property-read string $label
 * @property-read UnitSlot|ModelNotification|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Unit> $modelNotifications
 * @property-read int|null $model_notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AssignmentRecord> $primary_assignment_records
 * @property-read int|null $primary_assignment_records_count
 * @property-read string|null $relative_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AssignmentRecord> $secondary_assignment_records
 * @property-read int|null $secondary_assignment_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Slot> $slots
 * @property-read int|null $slots_count
 * @property-read string|null $url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $users
 * @property-read int|null $users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $usersViaSecondaryAssignmentRecords
 * @property-read int|null $users_via_secondary_assignment_records_count
 *
 * @method static \Database\Factories\UnitFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit hidden()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit visible()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereEmpty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
#[ScopedBy(UnitScope::class)]
class Unit extends Model implements HasLabel, Hideable, Sortable
{
    use CanBeHidden;
    use CanBeOrdered;
    use CanReceiveNotifications;
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasAssignmentRecords;
    use HasFactory;
    use HasIcon;
    use HasImages;
    use HasRelationships;
    use HasResourceLabel;
    use HasResourceUrl;
    use HasUsers;

    protected $attributes = [
        'hidden' => false,
    ];

    protected $fillable = [
        'name',
        'description',
        'empty',
        'order',
        'created_at',
        'updated_at',
    ];

    /**
     * @return BelongsToMany<Group, $this>
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'units_groups')
            ->as(UnitGroup::class);
    }

    /**
     * @return BelongsToMany<Slot, $this>
     */
    public function slots(): BelongsToMany
    {
        return $this->belongsToMany(Slot::class, 'units_slots')
            ->withPivot(['id'])
            ->using(UnitSlot::class)
            ->withTimestamps();
    }

    /**
     * @return HasManyDeep<User, $this>
     */
    public function usersViaSecondaryAssignmentRecords(): HasManyDeep
    {
        return $this->hasManyDeep(User::class, [AssignmentRecord::class], ['unit_id', 'id'], ['id', 'user_id'])
            ->with('secondary_assignment_records')
            ->where('records_assignments.type', AssignmentRecordType::SECONDARY);
    }
}
