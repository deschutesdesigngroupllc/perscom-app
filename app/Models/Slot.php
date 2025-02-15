<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Hideable;
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
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AssignmentRecord> $assignment_records
 * @property-read int|null $assignment_records_count
 * @property-read string $label
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slot position(\App\Models\Position $position)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slot query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slot specialty(\App\Models\Specialty $specialty)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slot visible()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slot whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slot whereEmpty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slot whereHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slot whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slot whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slot wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slot whereSpecialtyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slot whereUpdatedAt($value)
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

    public function assignment_records(): HasManyThrough
    {
        return $this->hasManyThrough(AssignmentRecord::class, UnitSlot::class, 'slot_id', 'unit_slot_id');
    }

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'units_slots')
            ->withPivot(['id'])
            ->using(UnitSlot::class)
            ->withTimestamps();
    }

    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, UnitSlot::class, 'slot_id', 'unit_slot_id');
    }
}
