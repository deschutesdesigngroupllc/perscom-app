<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Hideable;
use App\Models\Scopes\GroupScope;
use App\Traits\CanBeHidden;
use App\Traits\CanBeOrdered;
use App\Traits\CanReceiveNotifications;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasIcon;
use App\Traits\HasImages;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\EloquentSortable\Sortable;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;

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
 * @property-read Image|null $image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Image> $images
 * @property-read int|null $images_count
 * @property-read string $label
 * @property-read ModelNotification|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Group> $modelNotifications
 * @property-read int|null $model_notifications_count
 * @property-read string|null $relative_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Unit> $units
 * @property-read int|null $units_count
 * @property-read string|null $url
 *
 * @method static \Database\Factories\GroupFactory factory($count = null, $state = [])
 * @method static Builder<static>|Group forAutomaticRoster(?string $groupId = null)
 * @method static Builder<static>|Group forManualRoster(?string $groupId = null)
 * @method static Builder<static>|Group hidden()
 * @method static Builder<static>|Group newModelQuery()
 * @method static Builder<static>|Group newQuery()
 * @method static Builder<static>|Group ordered(string $direction = 'asc')
 * @method static Builder<static>|Group query()
 * @method static Builder<static>|Group visible()
 * @method static Builder<static>|Group whereCreatedAt($value)
 * @method static Builder<static>|Group whereDescription($value)
 * @method static Builder<static>|Group whereEmpty($value)
 * @method static Builder<static>|Group whereHidden($value)
 * @method static Builder<static>|Group whereIcon($value)
 * @method static Builder<static>|Group whereId($value)
 * @method static Builder<static>|Group whereName($value)
 * @method static Builder<static>|Group whereOrder($value)
 * @method static Builder<static>|Group whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
#[ScopedBy(GroupScope::class)]
class Group extends Model implements HasLabel, Hideable, Sortable
{
    use CanBeHidden;
    use CanBeOrdered;
    use CanReceiveNotifications;
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasFactory;
    use HasIcon;
    use HasImages;
    use HasResourceLabel;
    use HasResourceUrl;

    protected $attributes = [
        'hidden' => false,
    ];

    protected $fillable = [
        'name',
        'description',
        'empty',
        'order',
        'icon',
        'created_at',
        'updated_at',
    ];

    public function scopeForAutomaticRoster(Builder $query, ?string $groupId = null): void
    {
        $query
            ->when(! is_null($groupId), fn (Builder $query) => $query->where('groups.id', $groupId))
            ->with([
                'units.users' => function ($query): void {
                    /** @var User|HasMany $query */
                    /** @phpstan-ignore larastan.relationExistence */
                    $query
                        ->orderForRoster()
                        ->with([
                            'position',
                            'specialty',
                            'unit',
                            'rank',
                            'rank.image',
                            'status',
                        ]);
                },
            ])
            ->with([
                'units.secondary_assignment_records.user' => function ($query): void {
                    /** @var User|HasManyDeep $query */
                    /** @phpstan-ignore larastan.relationExistence */
                    $query->orderForRoster()
                        ->with([
                            'position',
                            'specialty',
                            'unit',
                            'rank',
                            'rank.image',
                            'status',
                        ]);
                },
            ]);
    }

    public function scopeForManualRoster(Builder $query, ?string $groupId = null): void
    {
        $query
            ->when(! is_null($groupId), fn (Builder $query) => $query->where('groups.id', $groupId))
            ->with([
                'units.slots.users' => function ($query): void {
                    /** @var User $query */
                    $query->orderForRoster();
                },
            ])
            ->with([
                'units.secondary_assignment_records.user' => function ($query): void {
                    /** @var User|HasManyDeep $query */
                    $query->orderForRoster();
                },
            ]);
    }

    /**
     * @return BelongsToMany<Unit, $this>
     */
    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'units_groups')
            ->as(UnitGroup::class);
    }
}
