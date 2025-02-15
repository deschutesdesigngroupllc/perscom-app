<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasSlot;
use App\Traits\HasUnit;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int|null $unit_id
 * @property int|null $slot_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AssignmentRecord> $assignment_records
 * @property-read int|null $assignment_records_count
 * @property-read Slot|null $slot
 * @property-read Unit|null $unit
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitSlot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitSlot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitSlot query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitSlot slot(\App\Models\Slot $slot)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitSlot unit(\App\Models\Unit $unit)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitSlot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitSlot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitSlot whereSlotId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitSlot whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitSlot whereUpdatedAt($value)
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

    public function assignment_records(): HasMany
    {
        return $this->hasMany(AssignmentRecord::class, 'unit_slot_id');
    }
}
