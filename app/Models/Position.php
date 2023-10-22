<?php

namespace App\Models;

use App\Models\Scopes\PositionScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * App\Models\Position
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssignmentRecord> $assignment_records
 * @property-read int|null $assignment_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 *
 * @method static \Database\Factories\PositionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Position newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Position newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Position ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Position query()
 *
 * @mixin \Eloquent
 */
class Position extends Model implements Sortable
{
    use HasFactory;
    use SortableTrait;

    protected static function booted(): void
    {
        static::addGlobalScope(new PositionScope());
    }

    /**
     * @var string[]
     */
    protected $fillable = ['name', 'description', 'order'];

    public function assignment_records(): HasMany
    {
        return $this->hasMany(AssignmentRecord::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
