<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\EventRegistrationStatus;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasUser;
use Exception;
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
 * @property EventRegistrationStatus|null $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Support\Facades\Event $event
 * @property-read User $user
 *
 * @method static Builder<static>|EventRegistration future()
 * @method static Builder<static>|EventRegistration newModelQuery()
 * @method static Builder<static>|EventRegistration newQuery()
 * @method static Builder<static>|EventRegistration query()
 * @method static Builder<static>|EventRegistration user(\App\Models\User $user)
 * @method static Builder<static>|EventRegistration whereCreatedAt($value)
 * @method static Builder<static>|EventRegistration whereEventId($value)
 * @method static Builder<static>|EventRegistration whereId($value)
 * @method static Builder<static>|EventRegistration whereStatus($value)
 * @method static Builder<static>|EventRegistration whereUpdatedAt($value)
 * @method static Builder<static>|EventRegistration whereUserId($value)
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
class EventRegistration extends Pivot
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasUser;

    protected $table = 'events_registrations';

    protected $fillable = [
        'event_id',
        'status',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (EventRegistration $eventRegistration): void {
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
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
