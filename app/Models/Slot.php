<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Hideable;
use App\Models\Scopes\SlotScope;
use App\Traits\CanBeHidden;
use App\Traits\CanBeOrdered;
use App\Traits\HasUsers;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\EloquentSortable\Sortable;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AssignmentRecord> $assignment_records
 * @property-read int|null $assignment_records_count
 * @property-read Position|null $position
 * @property-read Specialty|null $specialty
 * @property-read UnitSlot|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Unit> $units
 * @property-read int|null $units_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $users
 * @property-read int|null $users_count
 *
 * @method static \Database\Factories\SlotFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slot hidden()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slot ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slot query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slot visible()
 *
 * @mixin \Eloquent
 */
#[ScopedBy(SlotScope::class)]
class Slot extends Model implements Hideable, Sortable
{
    use CanBeHidden;
    use CanBeOrdered;
    use HasFactory;
    use HasUsers;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'position_id',
        'speciality_id',
        'name',
        'description',
        'empty',
    ];

    public function assignment_records(): HasManyThrough
    {
        return $this->hasManyThrough(AssignmentRecord::class, UnitSlot::class, 'slot_id', 'unit_slot_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'units_slots')
            ->withPivot(['id'])
            ->using(UnitSlot::class);
    }
}
