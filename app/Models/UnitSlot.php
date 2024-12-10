<?php

declare(strict_types=1);

namespace App\Models;

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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssignmentRecord> $assignment_records
 * @property-read int|null $assignment_records_count
 * @property-read Slot|null $slot
 * @property-read Unit|null $unit
 *
 * @method static \Illuminate\Database\Eloquent\Builder|UnitSlot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UnitSlot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UnitSlot query()
 * @method static \Illuminate\Database\Eloquent\Builder|UnitSlot slot(\App\Models\Slot $slot)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitSlot unit(\App\Models\Unit $unit)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitSlot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitSlot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitSlot whereSlotId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitSlot whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitSlot whereUpdatedAt($value)
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
