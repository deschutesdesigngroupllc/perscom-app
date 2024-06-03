<?php

namespace App\Models;

use App\Models\Enums\AssignmentRecordType;
use App\Models\Scopes\SpecialtyScope;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * App\Models\Specialty
 *
 * @property int $id
 * @property string $name
 * @property string|null $abbreviation
 * @property string|null $description
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssignmentRecord> $assignment_records
 * @property-read int|null $assignment_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssignmentRecord> $primary_assignment_records
 * @property-read int|null $primary_assignment_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssignmentRecord> $secondary_assignment_records
 * @property-read int|null $secondary_assignment_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 *
 * @method static \Database\Factories\SpecialtyFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty query()
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Specialty extends Model implements Sortable
{
    use ClearsResponseCache;
    use HasFactory;
    use SoftDeletes;
    use SortableTrait;

    /**
     * @var array<int, string>
     */
    protected $fillable = ['name', 'abbreviation', 'description', 'order', 'updated_at', 'created_at'];

    protected static function booted(): void
    {
        static::addGlobalScope(new SpecialtyScope());
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
