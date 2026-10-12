<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use App\Exceptions\EventRegistrationException;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Event;
use App\Models\User;
use App\Services\EventOccurrenceService;
use App\Services\EventRegistrationService;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;

/**
 * Lets a Livewire component record the signed-in member's RSVP to an occurrence, as used by
 * the x-events.rsvp-buttons component.
 */
trait RespondsToEvents
{
    public function respondToEvent(int $eventId, int $starts, string $status, bool $wholeSeries = false): void
    {
        $user = Auth::user();
        $event = Event::query()->find($eventId);

        abort_unless($user instanceof User && $event instanceof Event && $user->can('view', $event), 403);

        $occurrence = EventOccurrenceService::find($event, Date::createFromTimestamp($starts, config('app.timezone')));
        $response = EventRegistrationStatus::tryFrom($status);

        abort_unless($occurrence !== null && $response instanceof EventRegistrationStatus, 404);

        try {
            EventRegistrationService::respond($occurrence, $user, $response, $wholeSeries);

            Notification::make()
                ->success()
                ->title(sprintf('You responded %s.', $response->getMemberLabel()))
                ->send();
        } catch (EventRegistrationException $eventRegistrationException) {
            Notification::make()
                ->danger()
                ->title('Unable to save your response.')
                ->body($eventRegistrationException->getMessage())
                ->send();
        }

        $this->dispatch('event-response-updated', eventId: $eventId, starts: $starts);
    }
}
