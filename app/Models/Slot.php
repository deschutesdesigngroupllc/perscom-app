<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Hideable;
use App\Models\Enums\AssignmentRecordType;
use App\Models\Scopes\SlotScope;
use App\Traits\CanBeHidden;
use App\Traits\CanBeOrdered;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasPosition;
use App\Traits\HasResourceLabel;
use App\Traits\HasSpecialty;
use App\Traits\HasUsers;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;
use Spatie\EloquentSortable\Sortable;

/**
 * @property int $id
 * @property string $name
 * @property int|null $position_id
 * @property int|null $specialty_id
 * @property string|null $description
 * @property string|null $empty
 * @property int $order
 * @property bool $hidden
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, AssignmentRecord> $assignment_records
 * @property-read int|null $assignment_records_count
 * @property-read string $label
 * @property-read Position|null $position
 * @property-read Collection<int, AssignmentRecord> $primary_assignment_records
 * @property-read int|null $primary_assignment_records_count
 * @property-read Collection<int, AssignmentRecord> $secondary_assignment_records
 * @property-read int|null $secondary_assignment_records_count
 * @property-read Specialty|null $specialty
 * @property-read UnitSlot|null $pivot
 * @property-read Collection<int, Unit> $units
 * @property-read int|null $units_count
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 *
 * @method static \Database\Factories\SlotFactory factory($count = null, $state = [])
 * @method static Builder<static>|Slot hidden()
 * @method static Builder<static>|Slot newModelQuery()
 * @method static Builder<static>|Slot newQuery()
 * @method static Builder<static>|Slot ordered(string $direction = 'asc')
 * @method static Builder<static>|Slot position(\App\Models\Position $position)
 * @method static Builder<static>|Slot query()
 * @method static Builder<static>|Slot specialty(\App\Models\Specialty $specialty)
 * @method static Builder<static>|Slot visible()
 * @method static Builder<static>|Slot whereCreatedAt($value)
 * @method static Builder<static>|Slot whereDescription($value)
 * @method static Builder<static>|Slot whereEmpty($value)
 * @method static Builder<static>|Slot whereHidden($value)
 * @method static Builder<static>|Slot whereId($value)
 * @method static Builder<static>|Slot whereName($value)
 * @method static Builder<static>|Slot whereOrder($value)
 * @method static Builder<static>|Slot wherePositionId($value)
 * @method static Builder<static>|Slot whereSpecialtyId($value)
 * @method static Builder<static>|Slot whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
#[ScopedBy(SlotScope::class)]
class Slot extends Model implements Hideable, Sortable
{
    use CanBeHidden;
    use CanBeOrdered;
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasFactory;
    use HasPosition;
    use HasResourceLabel;
    use HasSpecialty;
    use HasUsers;

    protected $fillable = [
        'name',
        'description',
        'empty',
    ];

    /**
     * @return HasManyThrough<AssignmentRecord, UnitSlot, $this>
     */
    public function assignment_records(): HasManyThrough
    {
        return $this->hasManyThrough(AssignmentRecord::class, UnitSlot::class, 'slot_id', 'unit_slot_id');
    }

    /**
     * @return HasManyThrough<AssignmentRecord, UnitSlot, $this>
     */
    public function primary_assignment_records(): HasManyThrough
    {
        return $this->assignment_records()
            ->where('records_assignments.type', AssignmentRecordType::PRIMARY);
    }

    /**
     * @return HasManyThrough<AssignmentRecord, UnitSlot, $this>
     */
    public function secondary_assignment_records(): HasManyThrough
    {
        return $this->assignment_records()
            ->where('records_assignments.type', AssignmentRecordType::SECONDARY);
    }

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'units_slots')
            ->withPivot(['id'])
            ->using(UnitSlot::class)
            ->withTimestamps();
    }

    /**
     * @return HasManyThrough<User, UnitSlot, $this>
     */
    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, UnitSlot::class, 'slot_id', 'unit_slot_id');
    }
}
