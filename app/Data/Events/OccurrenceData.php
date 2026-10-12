<?php

declare(strict_types=1);

namespace App\Data\Events;

use App\Models\Event;
use App\Models\EventOccurrence;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class OccurrenceData extends Data
{
    public function __construct(
        public Event $event,
        public Carbon $starts,
        public Carbon $ends,
        public bool $allDay,
        public ?EventOccurrence $occurrence = null,
    ) {}

    public function isCancelled(): bool
    {
        return (bool) $this->occurrence?->is_cancelled;
    }

    public function key(): string
    {
        return sprintf('%s|%s', $this->event->getKey(), $this->starts->getTimestamp());
    }
}
