<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\NotificationChannel;
use App\Observers\EventObserver;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasAttachments;
use App\Traits\HasAuthor;
use App\Traits\HasComments;
use App\Traits\HasImages;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use App\Traits\HasSchedule;
use App\Traits\HasTags;
use Carbon\CarbonInterval;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Optional;

/**
 * @property int $id
 * @property string $name
 * @property int|null $calendar_id
 * @property string|null $description
 * @property string|null $content
 * @property string|null $location
 * @property string $url
 * @property int|null $author_id
 * @property bool $all_day
 * @property \Illuminate\Support\Carbon $starts
 * @property \Illuminate\Support\Carbon|null $ends
 * @property bool $repeats
 * @property string|null $by_set_position
 * @property string|null $by_year_day
 * @property bool $registration_enabled
 * @property \Illuminate\Support\Carbon|null $registration_deadline
 * @property bool $notifications_enabled
 * @property array|null $notifications_interval
 * @property AsEnumCollection|null $notifications_channels
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read User|null $author
 * @property-read Calendar|null $calendar
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read Image|null $image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Image> $images
 * @property-read int|null $images_count
 * @property-read string $label
 * @property-read CarbonInterval|null $length
 * @property-read EventRegistration $registration
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $registrations
 * @property-read int|null $registrations_count
 * @property-read Optional|string|null|null $relative_url
 * @property-read Optional|string|null|null $resource_url
 * @property-read Schedule|null $schedule
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
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereBySetPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereByYearDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCalendarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereEnds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereNotificationsChannels($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereNotificationsEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereNotificationsInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereRegistrationDeadline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereRegistrationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereRepeats($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereStarts($value)
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
    use HasResourceUrl {
        url as resourceUrl;
    }
    use HasSchedule;
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
        'starts',
        'ends',
        'repeats',
        'registration_enabled',
        'registration_deadline',
        'notifications_enabled',
        'notifications_interval',
        'notifications_channels',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @return Attribute<?CarbonInterval, void>
     */
    public function length(): Attribute
    {
        return Attribute::get(
            fn () => optional($this->ends, function () {
                return $this->starts->diff($this->ends);
            }) ?? null
        )->shouldCache();
    }

    /**
     * @return Attribute<string, void>
     */
    public function url(): Attribute
    {
        return Attribute::get(function ($value): string {
            if (filled($value)) {
                return $value;
            }

            return call_user_func($this->resourceUrl()->get, $value, $this->attributes);
        })->shouldCache();
    }

    /**
     * @return BelongsTo<Calendar, Event>
     */
    public function calendar(): BelongsTo
    {
        return $this->belongsTo(Calendar::class);
    }

    /**
     * @return BelongsToMany<User>
     */
    public function registrations(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'events_registrations')
            ->withPivot(['id', 'user_id', 'event_id'])
            ->as('registration')
            ->using(EventRegistration::class)
            ->withTimestamps();
    }

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        $casts = [
            'all_day' => 'boolean',
            'starts' => 'datetime',
            'ends' => 'datetime',
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
            'notifications_enabled' => 'boolean',
            'notifications_interval' => 'array',
            'notifications_channels' => AsEnumCollection::of(NotificationChannel::class),
        ];

        if ($this->all_day) {
            $casts['starts'] = 'date';
            $casts['ends'] = 'date';
        }

        return $casts;
    }
}
