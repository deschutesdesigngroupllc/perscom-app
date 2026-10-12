<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Event;
use App\Models\EventOccurrence;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Eloquent
 */
trait HasEvent
{
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function eventOccurrence(): BelongsTo
    {
        return $this->belongsTo(EventOccurrence::class);
    }

    protected function scopeEvent(Builder $query, Event $event): void
    {
        $query->whereBelongsTo($event);
    }

    protected function initializeHasEvent(): void
    {
        $this->mergeFillable([
            'event_id',
            'event_occurrence_id',
        ]);
    }
}
