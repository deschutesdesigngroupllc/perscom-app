<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\StatusScope;
use App\Traits\CanBeOrdered;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasAssignmentRecords;
use App\Traits\HasColorField;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use App\Traits\HasUsers;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;
use Spatie\EloquentSortable\Sortable;

/**
 * @property int $id
 * @property string $name
 * @property string $color
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, AssignmentRecord> $assignment_records
 * @property-read int|null $assignment_records_count
 * @property-read string $label
 * @property-read Collection<int, AssignmentRecord> $primary_assignment_records
 * @property-read int|null $primary_assignment_records_count
 * @property-read string|null $relative_url
 * @property-read Collection<int, AssignmentRecord> $secondary_assignment_records
 * @property-read int|null $secondary_assignment_records_count
 * @property-read Collection<int, Submission> $submissions
 * @property-read int|null $submissions_count
 * @property-read string|null $url
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 *
 * @method static \Database\Factories\StatusFactory factory($count = null, $state = [])
 * @method static Builder<static>|Status newModelQuery()
 * @method static Builder<static>|Status newQuery()
 * @method static Builder<static>|Status ordered(string $direction = 'asc')
 * @method static Builder<static>|Status query()
 * @method static Builder<static>|Status whereColor($value)
 * @method static Builder<static>|Status whereCreatedAt($value)
 * @method static Builder<static>|Status whereId($value)
 * @method static Builder<static>|Status whereName($value)
 * @method static Builder<static>|Status whereOrder($value)
 * @method static Builder<static>|Status whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
#[ScopedBy(StatusScope::class)]
class Status extends Model implements HasColor, HasLabel, Sortable
{
    use CanBeOrdered;
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasAssignmentRecords;
    use HasColorField;
    use HasFactory;
    use HasResourceLabel;
    use HasResourceUrl;
    use HasUsers;

    protected $fillable = [
        'name',
        'created_at',
        'updated_at',
    ];

    /**
     * @return MorphToMany<Submission, $this>
     */
    public function submissions(): MorphToMany
    {
        return $this->morphedByMany(Submission::class, 'model', 'model_has_statuses');
    }
}
