<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int|null $unit_id
 * @property int|null $slot_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|UnitSlot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UnitSlot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UnitSlot query()
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
    protected $table = 'units_slots';
}
