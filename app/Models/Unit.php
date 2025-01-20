<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Hideable;
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

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
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
 * @property-read ModelNotification $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Unit> $modelNotifications
 * @property-read int|null $model_notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AssignmentRecord> $primary_assignment_records
 * @property-read int|null $primary_assignment_records_count
 * @property-read string|null $relative_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AssignmentRecord> $secondary_assignment_records
 * @property-read int|null $secondary_assignment_records_count
 * @property-read string|null $url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $users
 * @property-read int|null $users_count
 *
 * @method static \Database\Factories\UnitFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Unit hidden()
 * @method static \Illuminate\Database\Eloquent\Builder|Unit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Unit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Unit ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Unit query()
 * @method static \Illuminate\Database\Eloquent\Builder|Unit visible()
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereUpdatedAt($value)
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
    use HasResourceLabel;
    use HasResourceUrl;
    use HasUsers;

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
        'created_at',
        'updated_at',
    ];

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'units_groups')
            ->withTimestamps()
            ->withPivot(['order'])
            ->ordered()
            ->as(Membership::class);
    }
}
