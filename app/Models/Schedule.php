<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\ScheduleEndType;
use App\Models\Enums\ScheduleFrequency;
use App\Services\RepeatService;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string|null $repeatable_type
 * @property int|null $repeatable_id
 * @property \Illuminate\Support\Carbon $start
 * @property ScheduleFrequency $frequency
 * @property int $interval
 * @property ScheduleEndType|null $end_type
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
 * @property-read bool $has_passed
 * @property-read Carbon|null $last_occurrence
 * @property-read Carbon|null $next_occurrence
 * @property-read Model|Eloquent|null $repeatable
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule query()
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereByDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereByMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereByMonthDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereBySetPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereByYearDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereEndType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereRepeatableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereRepeatableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereRrule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Schedule extends MorphPivot
{
    protected $table = 'schedules';

    /**
     * @return MorphTo<Model, Schedule>
     */
    public function repeatable(): MorphTo
    {
        return $this->morphTo('repeatable');
    }

    /**
     * @return Attribute<bool, void>
     */
    public function hasPassed(): Attribute
    {
        return Attribute::make(
            get: function (): bool {
                if (blank($this->last_occurrence)) {
                    return false;
                }

                // We need to add one minute, so we can actually do minute-by-minute
                // comparisons to now without missing it.
                return $this->last_occurrence->copy()->addMinute()->isPast();
            }
        )->shouldCache();
    }

    /**
     * @return Attribute<?Carbon, void>
     */
    public function lastOccurrence(): Attribute
    {
        return Attribute::make(
            get: function (): ?Carbon {
                return RepeatService::lastOccurrence($this);
            }
        )->shouldCache();
    }

    /**
     * @return Attribute<?Carbon, void>
     */
    public function nextOccurrence(): Attribute
    {
        return Attribute::make(
            get: function (): ?Carbon {
                return RepeatService::nextOccurrence($this);
            }
        )->shouldCache();
    }

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'start' => 'datetime',
            'frequency' => ScheduleFrequency::class,
            'interval' => 'integer',
            'end_type' => ScheduleEndType::class,
            'count' => 'integer',
            'until' => 'datetime',
            'by_day' => 'collection',
            'by_month_day' => 'collection',
            'by_month' => 'collection',
        ];
    }
}
