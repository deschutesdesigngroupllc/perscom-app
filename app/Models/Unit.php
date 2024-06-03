<?php

namespace App\Models;

use App\Models\Enums\AssignmentRecordType;
use App\Models\Scopes\UnitScope;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * App\Models\Unit
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssignmentRecord> $assignment_records
 * @property-read int|null $assignment_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Group> $groups
 * @property-read int|null $groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssignmentRecord> $primary_assignment_records
 * @property-read int|null $primary_assignment_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssignmentRecord> $secondary_assignment_records
 * @property-read int|null $secondary_assignment_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 *
 * @method static \Database\Factories\UnitFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Unit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Unit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Unit onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Unit ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Unit query()
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Unit withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Unit extends Model implements Sortable
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
        static::addGlobalScope(new UnitScope());
    }

    public function assignment_records(): HasMany
    {
        return $this->hasMany(AssignmentRecord::class);
    }

    public function primary_assignment_records(): HasMany
    {
        return $this->assignment_records()->where('type', AssignmentRecordType::PRIMARY);
    }

    public function secondary_assignment_records(): HasMany
    {
        return $this->assignment_records()->where('type', AssignmentRecordType::SECONDARY);
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'units_groups')
            ->withTimestamps()
            ->withPivot(['order'])
            ->ordered()
            ->as(Membership::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
