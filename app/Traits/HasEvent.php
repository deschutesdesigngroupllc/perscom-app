<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Event;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Eloquent
 */
trait HasEvent
{
    public function scopeEvent(Builder $query, Event $event): void
    {
        $query->whereBelongsTo($event);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    protected function initializeHasEvent(): void
    {
        $this->mergeFillable([
            'event_id',
        ]);
    }
}
