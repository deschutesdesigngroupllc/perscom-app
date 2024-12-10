<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $unit_id
 * @property int $group_id
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|UnitGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UnitGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UnitGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|UnitGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitGroup whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitGroup whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitGroup whereUpdatedAt($value)
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
