<?php

namespace App\Models;

use App\Models\Scopes\EventRegistrationScope;
use App\Traits\HasUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Znck\Eloquent\Traits\BelongsToThrough;

/**
 * App\Models\EventRegistration
 *
 * @property-read \App\Models\Event|null $event
 * @property-read \App\Models\User|null $user
 *
 * @method static Builder|EventRegistration forUser(\App\Models\User $user)
 * @method static Builder|EventRegistration future()
 * @method static Builder|EventRegistration newModelQuery()
 * @method static Builder|EventRegistration newQuery()
 * @method static Builder|EventRegistration query()
 *
 * @mixin \Eloquent
 */
class EventRegistration extends Pivot
{
    use BelongsToThrough;
    use HasFactory;
    use HasUser;
    use HasRelationships;

    /**
     * @var string
     */
    protected $table = 'events_registrations';

    /**
     * Boot
     */
    public static function boot()
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

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new EventRegistrationScope());
    }

    /**
     * @return Builder
     */
    public function scopeFuture(Builder $query)
    {
        return $query->whereRelation('event', function (Builder $query) {
            return $query->future();
        });
    }

    /**
     * @return \Znck\Eloquent\Relations\BelongsToThrough
     */
    public function calendar()
    {
        return $this->belongsToThrough(Calendar::class, Event::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * @return \Znck\Eloquent\Relations\BelongsToThrough
     */
    public function organizer()
    {
        return $this->belongsToThrough(User::class, Event::class, null, '', [User::class => 'author_id']);
    }

    /**
     * @return \Staudenmeir\EloquentHasManyDeep\HasManyDeep
     */
    public function attachments()
    {
        return $this->hasManyDeep(Attachment::class, [Event::class], [null, ['model_type', 'model_id']]);
    }

    /**
     * @return \Staudenmeir\EloquentHasManyDeep\HasManyDeep
     */
    public function images()
    {
        return $this->hasManyDeep(Image::class, [Event::class], [null, ['model_type', 'model_id']]);
    }
}
