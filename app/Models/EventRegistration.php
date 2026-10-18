<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\EventRegistrationStatus;
use App\Observers\EventRegistrationObserver;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasUser;
use Exception;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;

/**
 * App\Models\EventRegistration
 *
 * @property int $id
 * @property int $user_id
 * @property int $event_id
 * @property int|null $event_occurrence_id
 * @property EventRegistrationStatus|null $status
 * @property Carbon|null $checked_in_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Support\Facades\Event $event
 * @property-read EventOccurrence|null $occurrence
 * @property-read User $user
 *
 * @method static Builder<static>|EventRegistration future()
 * @method static Builder<static>|EventRegistration newModelQuery()
 * @method static Builder<static>|EventRegistration newQuery()
 * @method static Builder<static>|EventRegistration query()
 * @method static Builder<static>|EventRegistration user(\App\Models\User $user)
 * @method static Builder<static>|EventRegistration whereCheckedInAt($value)
 * @method static Builder<static>|EventRegistration whereCreatedAt($value)
 * @method static Builder<static>|EventRegistration whereEventId($value)
 * @method static Builder<static>|EventRegistration whereEventOccurrenceId($value)
 * @method static Builder<static>|EventRegistration whereId($value)
 * @method static Builder<static>|EventRegistration whereStatus($value)
 * @method static Builder<static>|EventRegistration whereUpdatedAt($value)
 * @method static Builder<static>|EventRegistration whereUserId($value)
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
#[ObservedBy(EventRegistrationObserver::class)]
class EventRegistration extends Pivot
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasUser;

    public $incrementing = true;

    protected $table = 'events_registrations';

    protected $fillable = [
        'event_id',
        'event_occurrence_id',
        'status',
        'checked_in_at',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function occurrence(): BelongsTo
    {
        return $this->belongsTo(EventOccurrence::class, 'event_occurrence_id');
    }

    /**
     * Attendance is recorded after the fact, so it is not subject to registration rules.
     */
    public function isAttendanceStatus(): bool
    {
        return in_array($this->status, [EventRegistrationStatus::ATTENDED, EventRegistrationStatus::NO_SHOW], true);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (EventRegistration $eventRegistration): void {
            if ($eventRegistration->isAttendanceStatus()) {
                return;
            }

            /** @var \Illuminate\Support\Facades\Event $event */
            $event = $eventRegistration->event;

            throw_if(! $event->registration_enabled,
                Exception::class,
                sprintf('Registrations for %s are disabled.', $event->name));

            throw_if(
                $event->registration_deadline &&
                Date::parse($event->registration_deadline)->isPast(),
                Exception::class,
                sprintf('The registration deadline for %s has passed.', $event->name));
        });
    }

    protected function scopeFuture(Builder $query): void
    {
        $query->whereRelation('event', function (Builder $query): void {
            /** @phpstan-ignore-next-line **/
            $query->future();
        });
    }

    /**
     * @return array<string, class-string<EventRegistrationStatus>|string>
     */
    protected function casts(): array
    {
        return [
            'status' => EventRegistrationStatus::class,
            'checked_in_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
