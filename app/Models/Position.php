<?php

namespace App\Models;

use App\Models\Enums\AssignmentRecordType;
use App\Models\Scopes\PositionScope;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * App\Models\Position
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssignmentRecord> $assignment_records
 * @property-read int|null $assignment_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssignmentRecord> $primary_assignment_records
 * @property-read int|null $primary_assignment_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssignmentRecord> $secondary_assignment_records
 * @property-read int|null $secondary_assignment_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 *
 * @method static \Database\Factories\PositionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Position newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Position newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Position ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Position query()
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Position extends Model implements Sortable
{
    use ClearsResponseCache;
    use HasFactory;
    use SortableTrait;

    /**
     * @var array<int, string>
     */
    protected $fillable = ['name', 'description', 'order', 'updated_at', 'created_at'];

    protected static function booted(): void
    {
        static::addGlobalScope(new PositionScope());
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

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
