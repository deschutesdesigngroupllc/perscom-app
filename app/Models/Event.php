<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\EventObserver;
use App\Services\EventService;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasAttachments;
use App\Traits\HasAuthor;
use App\Traits\HasComments;
use App\Traits\HasImages;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use App\Traits\HasTags;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Optional;
use RRule\RRule;

/**
 * @property int $id
 * @property string $name
 * @property int|null $calendar_id
 * @property string|null $description
 * @property string|null $content
 * @property string|null $location
 * @property Optional|string|null|null $url
 * @property int|null $author_id
 * @property bool $all_day
 * @property \Illuminate\Support\Carbon $start
 * @property mixed|null $end
 * @property bool $repeats
 * @property string|null $frequency
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
 * @property bool $registration_enabled
 * @property \Illuminate\Support\Carbon|null $registration_deadline
 * @property int $notifications_enabled
 * @property array|null $notifications_interval
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read User|null $author
 * @property-read Calendar|null $calendar
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read Optional|bool $has_passed
 * @property-read Image|null $image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Image> $images
 * @property-read int|null $images_count
 * @property-read string $label
 * @property-read Carbon|null $last_occurrence
 * @property-read CarbonInterval|Optional|null|null $length
 * @property-read Carbon|null $next_occurrence
 * @property-read EventRegistration $registration
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $registrations
 * @property-read int|null $registrations_count
 * @property-read Optional|string|null|null $relative_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Event author(\App\Models\User $user)
 * @method static \Database\Factories\EventFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Event onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereAllDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereByDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereByMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereByMonthDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereBySetPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereByYearDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCalendarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereEndType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereNotificationsEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereNotificationsInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereRegistrationDeadline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereRegistrationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereRepeats($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereRrule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Event withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[ObservedBy(EventObserver::class)]
class Event extends Model implements HasLabel
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasAttachments;
    use HasAuthor;
    use HasComments;
    use HasFactory;
    use HasImages;
    use HasResourceLabel;
    use HasResourceUrl;
    use HasTags;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'calendar_id',
        'description',
        'content',
        'location',
        'url',
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
        'registration_enabled',
        'registration_deadline',
        'notifications_enabled',
        'notifications_interval',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [
        'has_passed',
        'last_occurrence',
        'next_occurrence',
        //'length'
    ];

    public function lastOccurrence(): Attribute
    {
        return Attribute::make(
            get: fn (): ?Carbon => match (true) {
                ! $this->repeats => $this->start,
                $this->repeats && $this->end_type === 'on' && $this->until => $this->until,
                $this->repeats && $this->end_type === 'after' && $this->count => optional(EventService::generateRecurringRule($this), function (RRule $rule) {
                    return $rule->getNthOccurrenceAfter($this->start, $this->count)
                        ? Carbon::parse($rule->getNthOccurrenceAfter($this->start, $this->count))
                        : null;
                }),
                default => null
            }
        )->shouldCache();
    }

    public function end(): Attribute
    {
        return Attribute::make(
            set: function (mixed $value, array $attributes): ?Carbon {
                if (! data_get($attributes, 'all_day', false) && data_get($attributes, 'repeats', false)) {
                    $start = Carbon::parse(data_get($attributes, 'start'));

                    return Carbon::parse(data_get($attributes, 'end'))->set([
                        'day' => $start->day,
                        'month' => $start->month,
                        'year' => $start->year,
                    ]);
                }

                return Carbon::parse($value);
            }
        );
    }

    public function length(): Attribute
    {
        return Attribute::make(
            get: fn (): CarbonInterval|Optional|null => optional($this->end, function () {
                return $this->start->diff($this->end);
            }) ?: null
        )->shouldCache();
    }

    public function hasPassed(): Attribute
    {
        return Attribute::make(
            get: fn (): bool|Optional => optional($this->last_occurrence, static function (Carbon $end) {
                return $end->copy()->addMinute()->isPast();
            }) ?: false
        )->shouldCache();
    }

    public function nextOccurrence(): Attribute
    {
        return Attribute::make(
            get: fn (): ?Carbon => match (true) {
                $this->has_passed => null,
                ! $this->repeats => $this->start,
                $this->repeats => optional(EventService::generateRecurringRule($this), static function (RRule $rule) {
                    return Carbon::parse(collect($rule->getOccurrencesAfter(now(), false, 1))->first());
                })
            }
        )->shouldCache();
    }

    public function calendar(): BelongsTo
    {
        return $this->belongsTo(Calendar::class);
    }

    public function registrations(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'events_registrations')
            ->withPivot(['id', 'user_id', 'event_id'])
            ->as('registration')
            ->using(EventRegistration::class)
            ->withTimestamps();
    }

    protected function casts(): array
    {
        $casts = [
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
            'has_passed' => 'boolean',
            'last_occurrence' => 'datetime',
            'next_occurrence' => 'datetime',
            'registration_enabled' => 'boolean',
            'registration_deadline' => 'datetime',
            'notifications_interval' => 'array',
        ];

        if ($this->all_day) {
            $casts['start'] = 'date';
            $casts['end'] = 'date';
        }

        return $casts;
    }
}
