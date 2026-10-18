<?php

declare(strict_types=1);

namespace App\Data\Events;

use App\Filament\App\Resources\EventResource;
use App\Models\Event;
use App\Models\EventOccurrence;
use App\Services\ScheduleService;
use App\Services\UserSettingsService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
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
        return $this->toLocal($this->starts);
    }

    public function localEnds(): Carbon
    {
        return $this->toLocal($this->ends);
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

    /**
     * Timed occurrences are shown in the viewer's timezone. All-day occurrences keep the calendar
     * date they fall on in the organization's timezone, so every viewer sees the same day.
     */
    protected function toLocal(Carbon $date): Carbon
    {
        if (! $this->allDay) {
            return $date->copy()->setTimezone(UserSettingsService::timezone());
        }

        return Date::parse($date->copy()->setTimezone(ScheduleService::timezone())->toDateTimeString(), UserSettingsService::timezone());
    }
}
