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
use Spatie\EloquentSortable\Sortable;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $order
 * @property bool $hidden
 * @property string|null $icon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Image|null $image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Image> $images
 * @property-read int|null $images_count
 * @property-read string $label
 * @property-read ModelNotification $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Group> $modelNotifications
 * @property-read int|null $model_notifications_count
 * @property-read string|null $relative_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Unit> $units
 * @property-read int|null $units_count
 * @property-read string|null $url
 *
 * @method static \Database\Factories\GroupFactory factory($count = null, $state = [])
 * @method static Builder|Group hidden()
 * @method static Builder|Group newModelQuery()
 * @method static Builder|Group newQuery()
 * @method static Builder|Group orderForRoster(?string $groupId = null)
 * @method static Builder|Group ordered(string $direction = 'asc')
 * @method static Builder|Group query()
 * @method static Builder|Group visible()
 * @method static Builder|Group whereCreatedAt($value)
 * @method static Builder|Group whereDescription($value)
 * @method static Builder|Group whereHidden($value)
 * @method static Builder|Group whereIcon($value)
 * @method static Builder|Group whereId($value)
 * @method static Builder|Group whereName($value)
 * @method static Builder|Group whereOrder($value)
 * @method static Builder|Group whereUpdatedAt($value)
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

    /**
     * @var false[]
     */
    protected $attributes = [
        'hidden' => false,
    ];

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'order',
        'icon',
        'created_at',
        'updated_at',
    ];

    /**
     * @param  Builder<Group>  $query
     */
    public function scopeOrderForRoster(Builder $query, ?string $groupId = null): void
    {
        $query
            ->when(! is_null($groupId), fn (Builder $query) => $query->where('groups.id', $groupId))
            ->with([
                'units.users' => function ($query) {
                    /** @var User $query */
                    $query->orderForRoster();
                },
            ]);
    }

    /**
     * @return BelongsToMany<Unit>
     */
    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'units_groups')
            ->withTimestamps()
            ->withPivot(['order'])
            ->ordered()
            ->as(Membership::class);
    }
}
