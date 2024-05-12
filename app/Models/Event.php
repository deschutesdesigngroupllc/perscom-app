<?php

namespace App\Models;

use App\Traits\ClearsResponseCache;
use App\Traits\HasAttachments;
use App\Traits\HasAuthor;
use App\Traits\HasImages;
use App\Traits\HasTags;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use RRule\RRule;

/**
 * App\Models\Event
 *
 * @property int $id
 * @property string $name
 * @property int|null $calendar_id
 * @property string|null $description
 * @property string|null $content
 * @property string|null $location
 * @property string|null $url
 * @property int|null $author_id
 * @property bool $all_day
 * @property \Illuminate\Support\Carbon $start
 * @property \Illuminate\Support\Carbon|null $end
 * @property bool $repeats
 * @property string|null $frequency
 * @property int $interval
 * @property string|null $end_type
 * @property int|null $count
 * @property \Illuminate\Support\Carbon|null $until
 * @property \Illuminate\Support\Collection|null $by_day
 * @property \Illuminate\Support\Collection|null $by_month
 * @property mixed|null $by_set_position
 * @property \Illuminate\Support\Collection|null $by_month_day
 * @property mixed|null $by_year_day
 * @property string|null $rrule
 * @property bool $registration_enabled
 * @property \Illuminate\Support\Carbon|null $registration_deadline
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\Models\User|null $author
 * @property-read \App\Models\Calendar|null $calendar
 * @property-read mixed|null $computed_end
 * @property-read mixed|null $human_readable_pattern
 * @property-read mixed|null $is_past
 * @property-read mixed|null $next_occurrence
 * @property-read \App\Models\Image|null $image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Image> $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $registrations
 * @property-read int|null $registrations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 *
 * @method static Builder|Event author(\App\Models\User $user)
 * @method static Builder|Event datePeriod(?mixed $start, ?mixed $end)
 * @method static \Database\Factories\EventFactory factory($count = null, $state = [])
 * @method static Builder|Event future()
 * @method static Builder|Event newModelQuery()
 * @method static Builder|Event newQuery()
 * @method static Builder|Event query()
 * @method static Builder|Event tags(?mixed $tag)
 * @method static Builder|Event whereAllDay($value)
 * @method static Builder|Event whereAuthorId($value)
 * @method static Builder|Event whereByDay($value)
 * @method static Builder|Event whereByMonth($value)
 * @method static Builder|Event whereByMonthDay($value)
 * @method static Builder|Event whereBySetPosition($value)
 * @method static Builder|Event whereByYearDay($value)
 * @method static Builder|Event whereCalendarId($value)
 * @method static Builder|Event whereContent($value)
 * @method static Builder|Event whereCount($value)
 * @method static Builder|Event whereCreatedAt($value)
 * @method static Builder|Event whereDescription($value)
 * @method static Builder|Event whereEnd($value)
 * @method static Builder|Event whereEndType($value)
 * @method static Builder|Event whereFrequency($value)
 * @method static Builder|Event whereId($value)
 * @method static Builder|Event whereInterval($value)
 * @method static Builder|Event whereLocation($value)
 * @method static Builder|Event whereName($value)
 * @method static Builder|Event whereRegistrationDeadline($value)
 * @method static Builder|Event whereRegistrationEnabled($value)
 * @method static Builder|Event whereRepeats($value)
 * @method static Builder|Event whereRrule($value)
 * @method static Builder|Event whereStart($value)
 * @method static Builder|Event whereUntil($value)
 * @method static Builder|Event whereUpdatedAt($value)
 * @method static Builder|Event whereUrl($value)
 *
 * @mixin \Eloquent
 */
class Event extends Model
{
    use ClearsResponseCache;
    use HasAttachments;
    use HasAuthor;
    use HasFactory;
    use HasImages;
    use HasTags;

    /**
     * @var array<int, string>
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
        'updated_at',
        'created_at',
    ];

    /**
     * @var array<string, string>
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
     * @var array<int, string>
     */
    protected $appends = [
        'is_past',
        'computed_end',
        'next_occurrence',
    ];

    /**
     * @var string[]
     */
    protected $with = ['calendar'];

    public static function boot(): void
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

    public function scopeDatePeriod(Builder $query, mixed $start, mixed $end): void
    {
        $period = CarbonPeriod::create(
            Carbon::parse($start),
            Carbon::parse($end)
        );

        $query->where(function (Builder $query) use ($period) {
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

    public function scopeFuture(Builder $query): void
    {
        $query->whereDate('start', '>', now())
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

    public function getComputedEndAttribute(): mixed
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

    public function getNextOccurrenceAttribute(): mixed
    {
        return match (true) {
            ! $this->repeats => $this->start,
            $this->repeats => optional($this->generateRRule(), static function (RRule $rule) {
                return Carbon::parse(collect($rule->getOccurrencesAfter(now(), false, 1))->first());
            })
        };
    }

    public function getIsPastAttribute(): mixed
    {
        return optional($this->computed_end, static function (Carbon $end) {
            return $end->isPast();
        }) ?: false;
    }

    public function getHumanReadablePatternAttribute(): mixed
    {
        return optional($this->generateRRule(), static function (RRule $rule) {
            return $rule->humanReadable();
        });
    }

    public function calendar(): BelongsTo
    {
        return $this->belongsTo(Calendar::class);
    }

    public function registrations(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'events_registrations')
            ->withPivot(['id'])
            ->as('registration')
            ->using(EventRegistration::class)
            ->withTimestamps();
    }

    /**
     * @return RRule<DateTime>|null
     */
    public function generateRRule(): ?RRule
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
