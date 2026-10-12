<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets;

use App\Data\Events\OccurrenceData;
use App\Filament\App\Actions\SubscribeToCalendarAction;
use App\Filament\App\Pages\Calendar;
use App\Livewire\Concerns\RespondsToEvents;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use App\Services\EventAttendanceService;
use App\Services\EventOccurrenceService;
use App\Services\EventRegistrationService;
use App\Services\UserSettingsService;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\CarbonInterface;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\Size;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

/**
 * The signed-in member's next event and their agenda for the coming week, with RSVP and check-in.
 */
class UpcomingEventsWidget extends Widget implements HasActions, HasSchemas
{
    use HasWidgetShield;
    use InteractsWithActions;
    use InteractsWithSchemas;
    use RespondsToEvents;

    /**
     * How many days ahead the agenda covers.
     */
    public const int DAYS_AHEAD = 7;

    protected static ?int $sort = -4;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.app.widgets.upcoming-events-widget';

    /**
     * @var Collection<int, OccurrenceData>|null
     */
    protected ?Collection $occurrences = null;

    /**
     * @var Collection<int, EventRegistration>|null
     */
    protected ?Collection $registrations = null;

    #[On('event-response-updated')]
    public function refreshEvents(): void
    {
        $this->occurrences = null;
        $this->registrations = null;
    }

    public function user(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }

    /**
     * Upcoming, uncancelled occurrences over the next week of events the member can view.
     *
     * @return Collection<int, OccurrenceData>
     */
    public function occurrences(): Collection
    {
        if ($this->occurrences instanceof Collection) {
            return $this->occurrences;
        }

        $user = $this->user();

        if (! $user->can('viewAny', Event::class)) {
            return $this->occurrences = collect();
        }

        return $this->occurrences = EventOccurrenceService::between(now(), now()->addDays(static::DAYS_AHEAD), Event::query()->with('image'))
            ->reject(fn (OccurrenceData $occurrence): bool => $occurrence->isCancelled() || $occurrence->hasEnded())
            ->filter(fn (OccurrenceData $occurrence): bool => $user->can('view', $occurrence->event))
            ->values();
    }

    /**
     * The occurrence featured at the top: one happening now, otherwise the soonest.
     */
    public function nextOccurrence(): ?OccurrenceData
    {
        return $this->occurrences()->first(fn (OccurrenceData $occurrence): bool => $occurrence->isHappeningNow())
            ?? $this->occurrences()->first();
    }

    /**
     * The remaining occurrences grouped under a day heading in the member's timezone.
     *
     * @return Collection<string, Collection<int, OccurrenceData>>
     */
    public function agenda(): Collection
    {
        $next = $this->nextOccurrence();

        return $this->occurrences()
            ->reject(fn (OccurrenceData $occurrence): bool => $next instanceof OccurrenceData && $occurrence->key() === $next->key())
            ->groupBy(fn (OccurrenceData $occurrence): string => $this->dayHeading($occurrence->localStarts()));
    }

    /**
     * The member's effective response to an occurrence. Registrations for every shown occurrence
     * are loaded at once: a response to the occurrence itself wins over a series-wide response.
     */
    public function statusFor(OccurrenceData $occurrence): ?EventRegistrationStatus
    {
        $this->registrations ??= EventRegistration::query()
            ->where('user_id', $this->user()->getKey())
            ->whereIn('event_id', $this->occurrences()->map(fn (OccurrenceData $occurrence): mixed => $occurrence->event->getKey())->unique()->values()->all())
            ->get();

        $occurrenceId = $occurrence->event->repeats ? $occurrence->occurrence?->getKey() : null;

        $registrations = $this->registrations->where('event_id', $occurrence->event->getKey());

        return ($occurrenceId !== null ? $registrations->firstWhere('event_occurrence_id', $occurrenceId) : null)?->status
            ?? $registrations->first(fn (EventRegistration $registration): bool => $registration->event_occurrence_id === null)?->status;
    }

    public function needsResponse(OccurrenceData $occurrence): bool
    {
        return ! $this->statusFor($occurrence) instanceof EventRegistrationStatus && EventRegistrationService::canRespond($occurrence);
    }

    public function canCheckIn(OccurrenceData $occurrence): bool
    {
        $status = $this->statusFor($occurrence);

        return $status !== EventRegistrationStatus::ATTENDED
            && ($status instanceof EventRegistrationStatus || $occurrence->event->registration_enabled)
            && EventAttendanceService::isWithinCheckInWindow($occurrence->event);
    }

    public function checkInUrl(OccurrenceData $occurrence): string
    {
        return EventAttendanceService::checkInUrl($occurrence->event);
    }

    /**
     * @return Collection<string, Collection<int, EventRegistration>>
     */
    public function responses(OccurrenceData $occurrence): Collection
    {
        return EventRegistrationService::responses($occurrence);
    }

    /**
     * @return array{going: int, maybe: int, not_going: int}
     */
    public function summary(OccurrenceData $occurrence): array
    {
        return EventRegistrationService::summary($occurrence);
    }

    public function countdown(OccurrenceData $occurrence): string
    {
        if ($occurrence->isHappeningNow()) {
            return 'Happening now';
        }

        return 'Starts in '.$occurrence->starts->diffForHumans(now(), CarbonInterface::DIFF_ABSOLUTE, parts: 1);
    }

    public function timeRange(OccurrenceData $occurrence): string
    {
        $starts = $occurrence->localStarts();
        $ends = $occurrence->localEnds();

        if ($occurrence->allDay) {
            return $starts->isSameDay($ends) ? $starts->format('l, F j').' · All day' : sprintf('%s – %s · All day', $starts->format('M j'), $ends->format('M j'));
        }

        if (! $ends->greaterThan($starts)) {
            return $starts->format('l, F j · g:i A T');
        }

        return $starts->isSameDay($ends)
            ? sprintf('%s · %s – %s', $starts->format('l, F j'), $starts->format('g:i A'), $ends->format('g:i A T'))
            : sprintf('%s – %s', $starts->format('D, M j g:i A'), $ends->format('D, M j g:i A T'));
    }

    public function externalUrl(OccurrenceData $occurrence): ?string
    {
        $url = $occurrence->event->getRawOriginal('url');

        return is_string($url) && filled($url) ? $url : null;
    }

    public function location(OccurrenceData $occurrence): ?string
    {
        return filled($occurrence->event->location) ? Str::squish($occurrence->event->location) : null;
    }

    public function calendarUrl(): ?string
    {
        return Calendar::canAccess() ? Calendar::getUrl() : null;
    }

    public function subscribeAction(): Action
    {
        return SubscribeToCalendarAction::make('subscribe')
            ->link()
            ->size(Size::Small);
    }

    protected function dayHeading(CarbonInterface $date): string
    {
        $today = now(UserSettingsService::timezone());

        return match (true) {
            $date->isSameDay($today) => 'Today',
            $date->isSameDay($today->copy()->addDay()) => 'Tomorrow',
            default => $date->format('l, M j'),
        };
    }
}
