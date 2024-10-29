<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\ScheduleEndType;
use App\Models\Enums\ScheduleFrequency;
use App\Services\RepeatService;
use Carbon\CarbonInterval;
use Eloquent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string|null $repeatable_type
 * @property int|null $repeatable_id
 * @property \Illuminate\Support\Carbon $start
 * @property int $duration
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
 * @property \Illuminate\Support\Carbon|null $next_occurrence
 * @property \Illuminate\Support\Carbon|null $last_occurrence
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read bool $has_passed
 * @property-read CarbonInterval $length
 * @property-read Model|Eloquent|null $repeatable
 *
 * @method static \Database\Factories\ScheduleFactory factory($count = null, $state = [])
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
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereEndType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereLastOccurrence($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereNextOccurrence($value)
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
    use HasFactory;

    protected $table = 'schedules';

    protected $attributes = [
        'duration' => 0,
    ];

    protected $fillable = [
        'start',
        'duration',
        'frequency',
        'interval',
        'end_type',
        'count',
        'until',
        'by_day',
        'by_month',
        'by_set_position',
        'by_month_day',
        'by_year_day',
        'rrule',
        'next_occurrence',
        'last_occurrence',
    ];

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
                // comparisons to now() without missing it.
                return $this->last_occurrence->copy()->addMinute()->isPast();
            }
        )->shouldCache();
    }

    /**
     * @return Attribute<CarbonInterval, void>
     */
    public function length(): Attribute
    {
        return Attribute::get(fn (): CarbonInterval => $this->start->diff($this->start->copy()->addHours($this->duration)))
            ->shouldCache();
    }

    protected static function booted(): void
    {
        static::creating(function (Schedule $schedule) {
            $schedule->forceFill([
                'next_occurrence' => RepeatService::nextOccurrence($schedule),
                'last_occurrence' => RepeatService::lastOccurrence($schedule),
            ]);
        });

        static::updating(function (Schedule $schedule) {
            $schedule->forceFill([
                'next_occurrence' => RepeatService::nextOccurrence($schedule),
                'last_occurrence' => RepeatService::lastOccurrence($schedule),
            ]);
        });
    }

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'start' => 'datetime',
            'duration' => 'integer',
            'frequency' => ScheduleFrequency::class,
            'interval' => 'integer',
            'end_type' => ScheduleEndType::class,
            'count' => 'integer',
            'until' => 'datetime',
            'by_day' => 'collection',
            'by_month_day' => 'collection',
            'by_month' => 'collection',
            'next_occurrence' => 'datetime',
            'last_occurrence' => 'datetime',
        ];
    }
}
