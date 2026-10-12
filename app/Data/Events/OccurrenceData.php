<?php

declare(strict_types=1);

namespace App\Data\Events;

use App\Filament\App\Resources\EventResource;
use App\Models\Event;
use App\Models\EventOccurrence;
use App\Services\UserSettingsService;
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

    public function localStarts(): Carbon
    {
        return $this->starts->copy()->setTimezone(UserSettingsService::timezone());
    }

    public function localEnds(): Carbon
    {
        return $this->ends->copy()->setTimezone(UserSettingsService::timezone());
    }

    public function hasEnded(): bool
    {
        return $this->ends->isPast();
    }

    public function isHappeningNow(): bool
    {
        return now()->between($this->starts, $this->ends);
    }

    /**
     * The URL of the event page focused on this occurrence.
     */
    public function url(): string
    {
        return EventResource::getUrl('view', ['record' => $this->event, 'occurrence' => $this->starts->getTimestamp()]);
    }

    public function key(): string
    {
        return sprintf('%s|%s', $this->event->getKey(), $this->starts->getTimestamp());
    }
}
