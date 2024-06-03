<?php

namespace App\Models;

use App\Models\Scopes\GroupScope;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * App\Models\Group
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Unit> $units
 * @property-read int|null $units_count
 *
 * @method static \Database\Factories\GroupFactory factory($count = null, $state = [])
 * @method static Builder|Group newModelQuery()
 * @method static Builder|Group newQuery()
 * @method static Builder|Group onlyTrashed()
 * @method static Builder|Group orderForRoster(?string $groupId = null)
 * @method static Builder|Group ordered(string $direction = 'asc')
 * @method static Builder|Group query()
 * @method static Builder|Group whereCreatedAt($value)
 * @method static Builder|Group whereDeletedAt($value)
 * @method static Builder|Group whereDescription($value)
 * @method static Builder|Group whereId($value)
 * @method static Builder|Group whereName($value)
 * @method static Builder|Group whereOrder($value)
 * @method static Builder|Group whereUpdatedAt($value)
 * @method static Builder|Group withTrashed()
 * @method static Builder|Group withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Group extends Model implements Sortable
{
    use ClearsResponseCache;
    use HasFactory;
    use SoftDeletes;
    use SortableTrait;

    /**
     * @var array<int, string>
     */
    protected $fillable = ['name', 'description', 'order', 'updated_at', 'created_at'];

    protected static function booted(): void
    {
        static::addGlobalScope(new GroupScope());
    }

    public function scopeOrderForRoster(Builder $query, ?string $groupId = null): void
    {
        $query
            ->when(! is_null($groupId), fn (Builder $query) => $query->where('groups.id', $groupId))
            ->with([
                'units.users' => function (HasMany $query) {
                    $query
                        ->select('users.*')
                        ->leftJoin('ranks', 'ranks.id', '=', 'users.rank_id')
                        ->leftJoin('positions', 'positions.id', '=', 'users.position_id')
                        ->leftJoin('specialties', 'specialties.id', '=', 'users.specialty_id')
                        ->orderBy('ranks.order')
                        ->orderBy('positions.order')
                        ->orderBy('specialties.order')
                        ->orderBy('users.name');
                },
            ]);
    }

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'units_groups')
            ->withTimestamps()
            ->withPivot(['order'])
            ->ordered()
            ->as(Membership::class);
    }
}
