<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $unit_id
 * @property int $group_id
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitGroup whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitGroup whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitGroup whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitGroup whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class UnitGroup extends Pivot
{
    use ClearsResponseCache;

    protected $table = 'units_groups';

    protected $fillable = [
        'unit_id',
        'group_id',
        'created_at',
        'updated_at',
    ];
}
