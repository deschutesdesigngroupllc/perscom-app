<?php

declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string|null $repeatable_type
 * @property int|null $repeatable_id
 * @property \Illuminate\Support\Carbon $start
 * @property string $frequency
 * @property int $interval
 * @property string|null $end_type
 * @property int|null $count
 * @property \Illuminate\Support\Carbon|null $until
 * @property \Illuminate\Support\Collection|null $by_day
 * @property \Illuminate\Support\Collection|null $by_month
 * @property string|null $by_set_position
 * @property \Illuminate\Support\Collection|null $by_month_day
 * @property string|null $by_year_day
 * @property string|null $rrule
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|Eloquent|null $repeatable
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Repeatable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Repeatable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Repeatable query()
 * @method static \Illuminate\Database\Eloquent\Builder|Repeatable whereByDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Repeatable whereByMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Repeatable whereByMonthDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Repeatable whereBySetPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Repeatable whereByYearDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Repeatable whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Repeatable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Repeatable whereEndType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Repeatable whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Repeatable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Repeatable whereInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Repeatable whereRepeatableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Repeatable whereRepeatableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Repeatable whereRrule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Repeatable whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Repeatable whereUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Repeatable whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Repeatable extends MorphPivot
{
    protected $table = 'repeatables';

    /**
     * @return MorphTo<Model, Repeatable>
     */
    public function repeatable(): MorphTo
    {
        return $this->morphTo('repeatable');
    }

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'start' => 'datetime',
            'interval' => 'integer',
            'count' => 'integer',
            'until' => 'datetime',
            'by_day' => 'collection',
            'by_month_day' => 'collection',
            'by_month' => 'collection',
        ];
    }
}
