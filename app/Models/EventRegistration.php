<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\EventRegistrationStatus;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasUser;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\EventRegistration
 *
 * @property int $id
 * @property int $user_id
 * @property int $event_id
 * @property EventRegistrationStatus|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Event $event
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
 * @mixin \Eloquent
 */
class EventRegistration extends Pivot
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasFactory;
    use HasUser;

    protected $table = 'events_registrations';

    protected $fillable = [
        'event_id',
        'status',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::creating(function (EventRegistration $eventRegistration): void {
            throw_if(! $eventRegistration->event->registration_enabled,
                Exception::class,
                "Registrations for {$eventRegistration->event->name} are disabled.");

            throw_if(
                $eventRegistration->event->registration_deadline &&
                Carbon::parse($eventRegistration->event->registration_deadline)->isPast(),
                Exception::class,
                "The registration deadline for {$eventRegistration->event->name} has passed.");
        });
    }

    public function scopeFuture(Builder $query): void
    {
        $query->whereRelation('event', function (Builder $query): void {
            /** @phpstan-ignore-next-line **/
            $query->future();
        });
    }

    /**
     * @return BelongsTo<Event, $this>
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * @return string[]
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
