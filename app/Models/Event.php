<?php

namespace App\Models;

use App\Traits\HasAttachments;
use App\Traits\HasAuthor;
use App\Traits\HasImages;
use App\Traits\HasResourceUrlAttribute;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RRule\RRule;

class Event extends Model
{
    use HasAuthor;
    use HasAttachments;
    use HasFactory;
    use HasImages;
    use HasResourceUrlAttribute;

    protected $fillable = [
        'name',
        'calendar_id',
        'description',
        'content',
        'location',
        'url',
        'author_id',
        'all_day',
        'start',
        'end',
        'repeats',
        'frequency',
        'interval',
        'end_type',
        'count',
        'until',
        'by_day',
        'by_month_day',
        'by_month',
        'rrule',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'all_day' => 'boolean',
        'start' => 'datetime',
        'end' => 'datetime',
        'repeats' => 'boolean',
        'interval' => 'integer',
        'count' => 'integer',
        'until' => 'datetime',
        'by_day' => 'collection',
        'by_month_day' => 'collection',
        'by_month' => 'collection',
        'is_past' => 'boolean',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'is_past',
        'url',
        'relative_url',
    ];

    /**
     * Boot
     */
    public static function boot()
    {
        parent::boot();

        static::saved(static function (Event $event) {
            if ($event->repeats) {
                $updates = [];
                $updates['rrule'] = optional($event->generateRRule(), static function (RRule $rule) {
                    return $rule->rfcString();
                });

                switch ($event->end_type) {
                    case 'never':
                        $updates = array_merge($updates, [
                            'count' => null,
                            'until' => null,
                        ]);
                        break;

                    case 'after':
                        $updates = array_merge($updates, [
                            'until' => null,
                        ]);
                        break;

                    case 'on':
                        $updates = array_merge($updates, [
                            'count' => null,
                        ]);
                        break;
                }

                switch ($event->frequency) {
                    case 'DAILY':
                        $updates = array_merge($updates, [
                            'by_day' => null,
                            'by_month_day' => null,
                            'by_month' => null,
                        ]);
                        break;

                    case 'WEEKLY':
                        $updates = array_merge($updates, [
                            'by_month_day' => null,
                            'by_month' => null,
                        ]);
                        break;

                    case 'MONTHLY':
                        $updates = array_merge($updates, [
                            'by_day' => null,
                            'by_month' => null,
                        ]);
                        break;

                    case 'YEARLY':
                        $updates = array_merge($updates, [
                            'by_day' => null,
                            'by_month_day' => null,
                        ]);
                        break;
                }

                $event->updateQuietly($updates);
            }
        });
    }

    /**
     * @param  Builder  $query
     * @param $start
     * @param $end
     * @return Builder
     */
    public function scopeForDatePeriod(Builder $query, $start, $end)
    {
        $period = CarbonPeriod::create(
            Carbon::parse($start),
            Carbon::parse($end)
        );

        return $query->where(function (Builder $query) use ($period) {
            $query->whereBetween('start', [$period->getStartDate(), $period->getEndDate()]);
        })->orWhere(function (Builder $query) use ($period) {
            $query->whereBetween('end', [$period->getStartDate(), $period->getEndDate()]);
        })->orWhere(function (Builder $query) use ($period) {
            $query->whereDate('start', '<=', $period->getStartDate())->whereDate(
                'end',
                '>=',
                $period->getEndDate());
        })->orWhere(function (Builder $query) use ($period) {
            $query->where('repeats', '=', true)
                  ->where('end_type', '=', 'never')
                  ->whereDate('start', '<=', $period->getEndDate());
        })->orWhere(function (Builder $query) use ($period) {
            $query->where('repeats', '=', true)
                  ->where('end_type', '=', 'on')
                  ->whereDate('until', '>=', $period->getStartDate());
        });
    }

    /**
     * @return bool
     */
    public function getIsPastAttribute()
    {
        return match (true) {
            ! $this->repeats && $this->end?->isPast() => true,
            $this->repeats && $this->until?->isPast() => true,
            default => false
        };
    }

    /**
     * @return CarbonPeriod
     */
    public function getPeriodAttribute()
    {
        return CarbonPeriod::create($this->start, $this->end);
    }

    /**
     * @return \Illuminate\Support\Optional|mixed|RRule
     */
    public function getHumanReadablePatternAttribute()
    {
        return optional($this->generateRRule(), function (RRule $rule) {
            return $rule->humanReadable();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function calendar()
    {
        return $this->belongsTo(Calendar::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'events_tags');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function registrations()
    {
        return $this->belongsToMany(User::class, 'events_registrations')
            ->withPivot(['id'])
            ->withTimestamps()
            ->as('registration')
            ->using(EventRegistration::class);
    }

    /**
     * @return RRule|null
     */
    protected function generateRRule()
    {
        $payload = [
            'DTSTART' => $this->start->toDateTimeString(),
            'FREQ' => $this->frequency,
            'INTERVAL' => $this->interval,
        ];

        switch ($this->frequency) {
            case 'WEEKLY':
                if ($this->by_day?->isNotEmpty()) {
                    $payload['BYDAY'] = $this->by_day->implode(',');
                }
                break;

            case 'MONTHLY':
                if ($this->by_day && $this->by_set_position) {
                    $payload['BYDAY'] = $this->by_day;
                    $payload['BYSETPOS'] = $this->by_set_position;
                } elseif ($this->by_month_day?->isNotEmpty()) {
                    $payload['BYMONTHDAY'] = $this->by_month_day->implode(',');
                }
                break;

            case 'YEARLY':
                if ($this->by_month?->isNotEmpty()) {
                    $payload['BYMONTH'] = $this->by_month->implode(',');
                }

                if ($this->by_day && $this->by_set_position) {
                    $payload['BYDAY'] = $this->by_day;
                    $payload['BYSETPOS'] = $this->by_set_position;
                }
                break;
        }

        if ($this->count && $this->end_type === 'after') {
            $payload['COUNT'] = $this->count;
        }

        if ($this->until && $this->end_type === 'on') {
            $payload['UNTIL'] = $this->until->toDateString();
        }

        return $this->repeats ? new RRule($payload) : null;
    }
}
