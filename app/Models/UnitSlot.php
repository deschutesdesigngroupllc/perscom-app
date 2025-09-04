<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasSlot;
use App\Traits\HasUnit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $unit_id
 * @property int|null $slot_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, AssignmentRecord> $assignment_records
 * @property-read int|null $assignment_records_count
 * @property-read Slot|null $slot
 * @property-read Unit|null $unit
 *
 * @method static Builder<static>|UnitSlot newModelQuery()
 * @method static Builder<static>|UnitSlot newQuery()
 * @method static Builder<static>|UnitSlot query()
 * @method static Builder<static>|UnitSlot slot(\App\Models\Slot $slot)
 * @method static Builder<static>|UnitSlot unit(\App\Models\Unit $unit)
 * @method static Builder<static>|UnitSlot whereCreatedAt($value)
 * @method static Builder<static>|UnitSlot whereId($value)
 * @method static Builder<static>|UnitSlot whereSlotId($value)
 * @method static Builder<static>|UnitSlot whereUnitId($value)
 * @method static Builder<static>|UnitSlot whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class UnitSlot extends Pivot
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasSlot;
    use HasUnit;

    protected $table = 'units_slots';

    /**
     * @return HasMany<AssignmentRecord, $this>
     */
    public function assignment_records(): HasMany
    {
        return $this->hasMany(AssignmentRecord::class, 'unit_slot_id');
    }
}
