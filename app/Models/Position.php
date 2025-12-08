<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\PositionScope;
use App\Traits\CanBeOrdered;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasAssignmentRecords;
use App\Traits\HasCategories;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use App\Traits\HasUsers;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\EloquentSortable\Sortable;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, AssignmentRecord> $assignment_records
 * @property-read int|null $assignment_records_count
 * @property-read Collection<int, Category> $categories
 * @property-read int|null $categories_count
 * @property-read PositionCategory|null $categoryPivot
 * @property-read string $label
 * @property-read Collection<int, AssignmentRecord> $primary_assignment_records
 * @property-read int|null $primary_assignment_records_count
 * @property-read string|null $relative_url
 * @property-read Collection<int, AssignmentRecord> $secondary_assignment_records
 * @property-read int|null $secondary_assignment_records_count
 * @property-read string|null $url
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 *
 * @method static \Database\Factories\PositionFactory factory($count = null, $state = [])
 * @method static Builder<static>|Position newModelQuery()
 * @method static Builder<static>|Position newQuery()
 * @method static Builder<static>|Position ordered(string $direction = 'asc')
 * @method static Builder<static>|Position query()
 * @method static Builder<static>|Position whereCreatedAt($value)
 * @method static Builder<static>|Position whereDescription($value)
 * @method static Builder<static>|Position whereId($value)
 * @method static Builder<static>|Position whereName($value)
 * @method static Builder<static>|Position whereOrder($value)
 * @method static Builder<static>|Position whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
#[ScopedBy(PositionScope::class)]
class Position extends Model implements HasLabel, Sortable
{
    use CanBeOrdered;
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasAssignmentRecords;
    use HasCategories;
    use HasFactory;
    use HasResourceLabel;
    use HasResourceUrl;
    use HasUsers;

    protected $fillable = [
        'name',
        'description',
        'order',
        'created_at',
        'updated_at',
    ];
}
