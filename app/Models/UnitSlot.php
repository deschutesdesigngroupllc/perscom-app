<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasSlot;
use App\Traits\HasUnit;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
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
 *
 * @mixin \Eloquent
 */
class UnitSlot extends Pivot
{
    use HasSlot;
    use HasUnit;

    protected $table = 'units_slots';

    public function assignment_records(): HasMany
    {
        return $this->hasMany(AssignmentRecord::class, 'unit_slot_id');
    }
}
