<?php

namespace App\Models;

use App\Models\Scopes\GroupScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * App\Models\Group
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Unit> $units
 * @property-read int|null $units_count
 *
 * @method static \Database\Factories\GroupFactory factory($count = null, $state = [])
 * @method static Builder|Group newModelQuery()
 * @method static Builder|Group newQuery()
 * @method static Builder|Group orderForRoster()
 * @method static Builder|Group ordered(string $direction = 'asc')
 * @method static Builder|Group query()
 *
 * @mixin \Eloquent
 */
class Group extends Model implements Sortable
{
    use HasFactory;
    use SortableTrait;

    /**
     * @var string[]
     */
    protected $fillable = ['name', 'description', 'order'];

    protected static function booted(): void
    {
        static::addGlobalScope(new GroupScope());
    }

    public function scopeOrderForRoster(Builder $query): void
    {
        $query->with([
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
