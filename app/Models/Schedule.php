<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\ScheduleEndType;
use App\Models\Enums\ScheduleFrequency;
use App\Services\ScheduleService;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use Carbon\CarbonInterval;
use Database\Factories\ScheduleFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string|null $repeatable_type
 * @property int|null $repeatable_id
 * @property Carbon $start
 * @property int $duration
 * @property ScheduleFrequency $frequency
 * @property int $interval
 * @property ScheduleEndType|null $end_type
 * @property int|null $count
 * @property Carbon|null $until
 * @property Collection<array-key, mixed>|null $by_day
 * @property Collection<array-key, mixed>|null $by_month
 * @property string|null $by_set_position
 * @property Collection<array-key, mixed>|null $by_month_day
 * @property string|null $by_year_day
 * @property string|null $rrule
 * @property Carbon|null $next_occurrence
 * @property Carbon|null $last_occurrence
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read bool $has_passed
 * @property-read CarbonInterval $length
 * @property-read Model|Eloquent|null $repeatable
 *
 * @method static ScheduleFactory factory($count = null, $state = [])
 * @method static Builder<static>|Schedule newModelQuery()
 * @method static Builder<static>|Schedule newQuery()
 * @method static Builder<static>|Schedule query()
 * @method static Builder<static>|Schedule whereByDay($value)
 * @method static Builder<static>|Schedule whereByMonth($value)
 * @method static Builder<static>|Schedule whereByMonthDay($value)
 * @method static Builder<static>|Schedule whereBySetPosition($value)
 * @method static Builder<static>|Schedule whereByYearDay($value)
 * @method static Builder<static>|Schedule whereCount($value)
 * @method static Builder<static>|Schedule whereCreatedAt($value)
 * @method static Builder<static>|Schedule whereDuration($value)
 * @method static Builder<static>|Schedule whereEndType($value)
 * @method static Builder<static>|Schedule whereFrequency($value)
 * @method static Builder<static>|Schedule whereId($value)
 * @method static Builder<static>|Schedule whereInterval($value)
 * @method static Builder<static>|Schedule whereLastOccurrence($value)
 * @method static Builder<static>|Schedule whereNextOccurrence($value)
 * @method static Builder<static>|Schedule whereRepeatableId($value)
 * @method static Builder<static>|Schedule whereRepeatableType($value)
 * @method static Builder<static>|Schedule whereRrule($value)
 * @method static Builder<static>|Schedule whereStart($value)
 * @method static Builder<static>|Schedule whereUntil($value)
 * @method static Builder<static>|Schedule whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Schedule extends MorphPivot
{
    use ClearsApiCache;
    use ClearsResponseCache;
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

    public function repeatable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return Attribute<bool, never>
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
     * @return Attribute<CarbonInterval, never>
     */
    public function length(): Attribute
    {
        return Attribute::get(fn (): CarbonInterval => $this->start->diff($this->start->copy()->addHours($this->duration)))
            ->shouldCache();
    }

    protected static function booted(): void
    {
        static::creating(function (Schedule $schedule): void {
            $schedule->forceFill([
                'next_occurrence' => ScheduleService::nextOccurrence($schedule),
                'last_occurrence' => ScheduleService::lastOccurrence($schedule),
            ]);
        });

        static::updating(function (Schedule $schedule): void {
            $schedule->forceFill([
                'next_occurrence' => ScheduleService::nextOccurrence($schedule),
                'last_occurrence' => ScheduleService::lastOccurrence($schedule),
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
