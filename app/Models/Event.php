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
 * @property bool $registration_enabled
 * @property \Illuminate\Support\Carbon|null $registration_deadline
 * @property bool $notifications_enabled
 * @property array<array-key, mixed>|null $notifications_interval
 * @property \Illuminate\Support\Collection<int, NotificationChannel>|null $notifications_channels
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Attachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read User|null $author
 * @property-read Calendar|null $calendar
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Comment> $comments
 * @property-read int|null $comments_count
 * @property-read bool $has_passed
 * @property-read Image|null $image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Image> $images
 * @property-read int|null $images_count
 * @property-read string $label
 * @property-read mixed $length
 * @property-read EventRegistration|null $registration
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $registrations
 * @property-read int|null $registrations_count
 * @property-read string|null $relative_url
 * @property-read string|null $resource_url
 * @property-read Schedule|null $schedule
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Tag> $tags
 * @property-read int|null $tags_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event author(\App\Models\User $user)
 * @method static \Database\Factories\EventFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereAllDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereCalendarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereEnds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereNotificationsChannels($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereNotificationsEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereNotificationsInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereRegistrationDeadline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereRegistrationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereRepeats($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereStarts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereUrl($value)
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

    protected $attributes = [
        'all_day' => false,
        'repeats' => false,
        'registration_enabled' => true,
        'notifications_enabled' => true,
    ];

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
    ];

    /**
     * @return Attribute<bool, never>
     */
    public function hasPassed(): Attribute
    {
        return Attribute::make(
            get: function (): bool {
                if (filled($this->schedule)) {
                    return $this->schedule->has_passed;
                }

                // We need to add one minute, so we can actually do minute-by-minute
                // comparisons to now() without missing it.
                return $this->ends->addMinute()->isPast();
            }
        )->shouldCache();
    }

    /**
     * @return Attribute<?CarbonInterval, never>
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
     * @return Attribute<string, never>
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
     * @return BelongsTo<Calendar, $this>
     */
    public function calendar(): BelongsTo
    {
        return $this->belongsTo(Calendar::class);
    }

    /**
     * @return BelongsToMany<User, $this>
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
