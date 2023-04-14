<?php

namespace App\Models;

use App\Models\Scopes\EventRegistrationScope;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Znck\Eloquent\Traits\BelongsToThrough;

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
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new EventRegistrationScope());
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeFuture(Builder $query)
    {
        return $query->whereRelation('event', function (Builder $query) {
            return $query->future();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
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
