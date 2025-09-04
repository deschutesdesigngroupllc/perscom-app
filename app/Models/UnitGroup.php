<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasGroup;
use App\Traits\HasUnit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $unit_id
 * @property int $group_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Group $group
 * @property-read Unit $unit
 *
 * @method static Builder<static>|UnitGroup group(\App\Models\Group $group)
 * @method static Builder<static>|UnitGroup newModelQuery()
 * @method static Builder<static>|UnitGroup newQuery()
 * @method static Builder<static>|UnitGroup query()
 * @method static Builder<static>|UnitGroup unit(\App\Models\Unit $unit)
 * @method static Builder<static>|UnitGroup whereCreatedAt($value)
 * @method static Builder<static>|UnitGroup whereGroupId($value)
 * @method static Builder<static>|UnitGroup whereId($value)
 * @method static Builder<static>|UnitGroup whereUnitId($value)
 * @method static Builder<static>|UnitGroup whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class UnitGroup extends Pivot
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasGroup;
    use HasUnit;

    protected $table = 'units_groups';

    protected $fillable = [
        'created_at',
        'updated_at',
    ];
}
