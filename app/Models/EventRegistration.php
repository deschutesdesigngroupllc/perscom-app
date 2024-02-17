<?php

namespace App\Models;

use App\Models\Scopes\EventRegistrationScope;
use App\Traits\ClearsResponseCache;
use App\Traits\HasUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Znck\Eloquent\Relations\BelongsToThrough as BelongsToThroughRelation;
use Znck\Eloquent\Traits\BelongsToThrough;

/**
 * App\Models\EventRegistration
 *
 * @property int $id
 * @property int $user_id
 * @property int $event_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Event $event
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Attachment[] $attachments
 * @property-read int|null $attachments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Image[] $images
 * @property-read int|null $images_count
 *
 * @method static Builder|EventRegistration future()
 * @method static Builder|EventRegistration newModelQuery()
 * @method static Builder|EventRegistration newQuery()
 * @method static Builder|EventRegistration query()
 * @method static Builder|EventRegistration user(\App\Models\User $user)
 * @method static Builder|EventRegistration whereCreatedAt($value)
 * @method static Builder|EventRegistration whereEventId($value)
 * @method static Builder|EventRegistration whereId($value)
 * @method static Builder|EventRegistration whereUpdatedAt($value)
 * @method static Builder|EventRegistration whereUserId($value)
 *
 * @mixin \Eloquent
 */
class EventRegistration extends Pivot
{
    use BelongsToThrough;
    use ClearsResponseCache;
    use HasFactory;
    use HasRelationships;
    use HasUser;

    /**
     * @var string
     */
    protected $table = 'events_registrations';

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::creating(function (EventRegistration $eventRegistration) {
            throw_if(! $eventRegistration->event->registration_enabled,
                \Exception::class,
                "Registrations for {$eventRegistration->event->name} are disabled.");

            throw_if(
                $eventRegistration->event->registration_deadline &&
                Carbon::parse($eventRegistration->event->registration_deadline)->isPast(),
                \Exception::class,
                "The registration deadline for {$eventRegistration->event->name} has passed.");
        });
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new EventRegistrationScope());
    }

    public function scopeFuture(Builder $query): void
    {
        $query->whereRelation('event', function (Builder $query) {
            $query->future();
        });
    }

    public function calendar(): BelongsToThroughRelation
    {
        return $this->belongsToThrough(Calendar::class, Event::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function organizer(): BelongsToThroughRelation
    {
        return $this->belongsToThrough(User::class, Event::class, null, '', [User::class => 'author_id']);
    }

    public function attachments(): HasManyDeep
    {
        return $this->hasManyDeep(Attachment::class, [Event::class], [null, ['model_type', 'model_id']]);
    }

    public function images(): HasManyDeep
    {
        return $this->hasManyDeep(Image::class, [Event::class], [null, ['model_type', 'model_id']]);
    }
}
