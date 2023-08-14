<?php

namespace App\Models;

use App\Traits\HasAttachments;
use App\Traits\HasAuthor;
use App\Traits\HasImages;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RRule\RRule;

/**
 * App\Models\Event
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\Models\User|null $author
 * @property-read \App\Models\Calendar|null $calendar
 * @property-read mixed|null $computed_end
 * @property-read \Illuminate\Support\Optional|mixed|\RRule\RRule $human_readable_pattern
 * @property-read false|\Illuminate\Support\Optional|mixed $is_past
 * @property-read \Illuminate\Support\Optional|mixed|\RRule\RRule|null $next_occurrence
 * @property-read \App\Models\Image|null $image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Image> $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $registrations
 * @property-read int|null $registrations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 *
 * @method static Builder|Event author($user)
 * @method static Builder|Event datePeriod($start, $end)
 * @method static \Database\Factories\EventFactory factory($count = null, $state = [])
 * @method static Builder|Event future()
 * @method static Builder|Event newModelQuery()
 * @method static Builder|Event newQuery()
 * @method static Builder|Event query()
 *
 * @mixin \Eloquent
 */
class Event extends Model
{
    use HasAuthor;
    use HasAttachments;
    use HasFactory;
    use HasImages;

    /**
     * @var string[]
     */
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
        'computed_end' => 'datetime',
        'next_occurrence' => 'datetime',
        'registration_enabled' => 'boolean',
        'registration_deadline' => 'datetime',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'is_past',
        'computed_end',
        'next_occurrence',
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

        static::deleted(static function (Event $event) {
            $event->registrations()->detach();
        });
    }

    /**
     * @return Builder
     */
    public function scopeDatePeriod(Builder $query, $start, $end)
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
     * @return Builder
     */
    public function scopeFuture(Builder $query)
    {
        return $query->whereDate('start', '>', now())
            ->orWhereDate('end', '>', now())
            ->orWhere(function (Builder $query) {
                $query->where('repeats', '=', true)
                    ->where('end_type', '=', 'never');
            })
            ->orWhere(function (Builder $query) {
                $query->where('repeats', '=', true)
                    ->where('end_type', '=', 'on')
                    ->whereDate('until', '>=', now());
            })
            ->orWhere(function (Builder $query) {
                $query->where('repeats', '=', true)
                    ->where('end_type', '=', 'after');
            });
    }

    /**
     * @return mixed|null
     */
    public function getComputedEndAttribute()
    {
        return match (true) {
            ! $this->repeats && $this->end => $this->end,
            $this->repeats && $this->end_type === 'on' && $this->until => $this->until,
            $this->repeats && $this->end_type === 'after' && $this->count => optional($this->generateRRule(), function (RRule $rule) {
                return Carbon::parse($rule->getNthOccurrenceAfter($this->start, $this->count));
            }),
            default => null
        };
    }

    /**
     * @return \Illuminate\Support\Optional|mixed|RRule|null
     */
    public function getNextOccurrenceAttribute()
    {
        return match (true) {
            ! $this->repeats => $this->start,
            $this->repeats => optional($this->generateRRule(), static function (RRule $rule) {
                return Carbon::parse(collect($rule->getOccurrencesAfter(now(), false, 1))->first());
            })
        };
    }

    /**
     * @return false|\Illuminate\Support\Optional|mixed
     */
    public function getIsPastAttribute()
    {
        return optional($this->computed_end, static function (Carbon $end) {
            return $end->isPast();
        }) ?: false;
    }

    /**
     * @return \Illuminate\Support\Optional|mixed|RRule
     */
    public function getHumanReadablePatternAttribute()
    {
        return optional($this->generateRRule(), static function (RRule $rule) {
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
            ->as('registration')
            ->using(EventRegistration::class)
            ->withTimestamps();
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
