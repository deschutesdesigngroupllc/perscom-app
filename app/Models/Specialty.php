<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\SpecialtyScope;
use App\Traits\CanBeOrdered;
use App\Traits\ClearsResponseCache;
use App\Traits\HasAssignmentRecords;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use App\Traits\HasUsers;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;

/**
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
 * @property-read string $label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssignmentRecord> $primary_assignment_records
 * @property-read int|null $primary_assignment_records_count
 * @property-read \Illuminate\Support\Optional|string|null|null $relative_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssignmentRecord> $secondary_assignment_records
 * @property-read int|null $secondary_assignment_records_count
 * @property-read \Illuminate\Support\Optional|string|null|null $url
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
#[ScopedBy(SpecialtyScope::class)]
class Specialty extends Model implements HasLabel, Sortable
{
    use CanBeOrdered;
    use ClearsResponseCache;
    use HasAssignmentRecords;
    use HasFactory;
    use HasResourceLabel;
    use HasResourceUrl;
    use HasUsers;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'abbreviation',
        'description',
        'order',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
