<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Event;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Eloquent
 *
 * @template TModel of Model
 */
trait HasEvent
{
    public function scopeEvent(Builder $query, Event $event): void
    {
        $query->whereBelongsTo($event);
    }

    /**
     * @return BelongsTo<Event, TModel>
     */
    public function event(): BelongsTo
    {
        /** @var Model $this */
        return $this->belongsTo(Event::class);
    }

    protected function initializeHasEvent(): void
    {
        $this->mergeFillable([
            'event_id',
        ]);
    }
}
