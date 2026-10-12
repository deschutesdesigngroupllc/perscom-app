<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\EventResource\Pages;

use App\Data\Events\OccurrenceData;
use App\Filament\App\Resources\EventResource;
use App\Filament\App\Resources\EventResource\Actions\CheckInLinkAction;
use App\Filament\App\Resources\EventResource\Actions\TakeAttendanceAction;
use App\Livewire\Concerns\RespondsToEvents;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use App\Services\EventOccurrenceService;
use App\Services\EventRegistrationService;
use App\Services\ScheduleService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

class ViewEvent extends ViewRecord
{
    use RespondsToEvents;

    /**
     * How many dates before the selected occurrence the occurrence picker lists.
     */
    public const int PICKER_PREVIOUS = 3;

    /**
     * How many dates after the selected occurrence the occurrence picker lists.
     */
    public const int PICKER_NEXT = 8;

    /**
     * The start of the selected occurrence as a unix timestamp. Defaults to the nearest occurrence.
     */
    #[Url]
    public ?int $occurrence = null;

    /**
     * Whether RSVPs from the action bar apply to every date of a repeating event.
     */
    public bool $wholeSeries = false;

    protected static string $resource = EventResource::class;

    protected ?OccurrenceData $resolvedOccurrence = null;

    public function getTitle(): string|Htmlable
    {
        return $this->getEvent()->name;
    }

    /**
     * The event name is shown in the page hero instead of the page header.
     */
    public function getHeading(): string|Htmlable
    {
        return '';
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                View::make('filament.app.events.view.hero')
                    ->columnSpanFull(),
                Grid::make(['default' => 1, 'lg' => 3])
                    ->columnSpanFull()
                    ->extraAttributes([
                        // Let the sidebar span the briefing and discussion rows on wide screens while it
                        // still sits between them when the columns stack on small screens.
                        'class' => 'lg:[&>.fi-grid]:grid-rows-[auto_1fr] lg:[&>.fi-grid>.fi-grid-col:nth-child(2)]:row-span-2',
                    ])
                    ->schema([
                        View::make('filament.app.events.view.briefing')
                            ->columnSpan(['default' => 1, 'lg' => 2]),
                        View::make('filament.app.events.view.sidebar')
                            ->columnSpan(['default' => 1, 'lg' => 1]),
                        $this->getRelationManagersContentComponent()
                            ->columnSpan(['default' => 1, 'lg' => 2]),
                    ]),
            ]);
    }

    public function updatedOccurrence(): void
    {
        $this->resolvedOccurrence = null;
    }

    #[On('event-response-updated')]
    #[On('refreshRegistrations')]
    public function refreshOccurrence(): void
    {
        $this->resolvedOccurrence = null;
    }

    public function getEvent(): Event
    {
        /** @var Event $event */
        $event = $this->getRecord();

        return $event;
    }

    /**
     * The occurrence the page is focused on, as chosen with the occurrence query parameter.
     */
    public function getSelectedOccurrence(): ?OccurrenceData
    {
        if ($this->resolvedOccurrence instanceof OccurrenceData) {
            return $this->resolvedOccurrence;
        }

        $event = $this->getEvent();

        $selected = filled($this->occurrence) && $event->repeats
            ? EventOccurrenceService::find($event, Date::createFromTimestamp($this->occurrence, config('app.timezone')))
            : null;

        return $this->resolvedOccurrence = $selected ?? EventOccurrenceService::nearest($event);
    }

    /**
     * The dates listed in the occurrence picker of a repeating event.
     *
     * @return Collection<int, OccurrenceData>
     */
    public function getPickerOccurrences(): Collection
    {
        $event = $this->getEvent();
        $selected = $this->getSelectedOccurrence();

        if (! $event->repeats || ! $selected instanceof OccurrenceData) {
            return collect();
        }

        return EventOccurrenceService::around($event, $selected->starts, static::PICKER_PREVIOUS + 1, static::PICKER_NEXT);
    }

    public function getRepeatRule(): ?string
    {
        $event = $this->getEvent();

        if (! $event->repeats || blank($event->schedule)) {
            return null;
        }

        return ScheduleService::getSchedulePattern($event->schedule, $event->all_day);
    }

    public function getViewerResponse(): ?EventRegistrationStatus
    {
        $user = Auth::user();
        $occurrence = $this->getSelectedOccurrence();

        if (! $user instanceof User || ! $occurrence instanceof OccurrenceData) {
            return null;
        }

        return EventRegistrationService::responseFor($occurrence, $user);
    }

    /**
     * Explains why members can not RSVP to the selected occurrence, or null when they can.
     */
    public function getResponseClosedMessage(): ?string
    {
        $event = $this->getEvent();
        $occurrence = $this->getSelectedOccurrence();

        return match (true) {
            ! $occurrence instanceof OccurrenceData => 'This event has no upcoming dates.',
            $occurrence->isCancelled() => 'This date has been cancelled.',
            $occurrence->hasEnded() => 'This date has ended.',
            ! $event->registration_enabled => 'RSVPs are not being collected for this event.',
            $event->registration_deadline?->isPast() === true => 'The RSVP deadline has passed.',
            default => null,
        };
    }

    /**
     * Member responses for the selected occurrence, grouped into the going, maybe and can't go lists.
     *
     * @return array{going: Collection<int, User>, maybe: Collection<int, User>, not_going: Collection<int, User>}
     */
    public function getAttendees(): array
    {
        $occurrence = $this->getSelectedOccurrence();
        $responses = $occurrence instanceof OccurrenceData ? EventRegistrationService::responses($occurrence) : collect();

        $usersFor = fn (EventRegistrationStatus ...$statuses): Collection => collect($statuses)
            ->flatMap(fn (EventRegistrationStatus $status): Collection => $responses->get($status->value, collect()))
            ->map(fn (EventRegistration $registration): mixed => $registration->getRelation('user'))
            ->filter(fn (mixed $user): bool => $user instanceof User)
            ->sortBy(fn (User $user): string => $user->name)
            ->values();

        return [
            'going' => $usersFor(EventRegistrationStatus::GOING, EventRegistrationStatus::ATTENDED),
            'maybe' => $usersFor(EventRegistrationStatus::TENTATIVE, EventRegistrationStatus::INTERESTED),
            'not_going' => $usersFor(EventRegistrationStatus::NOT_GOING, EventRegistrationStatus::DECLINED),
        ];
    }

    /**
     * The calendar color used for the hero fallback and date badges, limited to hex values so it is
     * safe to place in a style attribute.
     */
    public function getCalendarColor(): string
    {
        $color = $this->getEvent()->calendar?->color;

        return is_string($color) && preg_match('/^#[0-9a-fA-F]{3,8}$/', $color) ? $color : '#2563eb';
    }

    /**
     * The time of day the occurrence runs, in the viewer's timezone.
     */
    public function formatTimeRange(OccurrenceData $occurrence): string
    {
        $starts = $occurrence->localStarts();
        $ends = $occurrence->localEnds();

        if ($occurrence->allDay) {
            return $starts->isSameDay($ends) ? 'All day' : sprintf('All day, through %s', $ends->format('l, F j'));
        }

        if ($ends->lessThanOrEqualTo($starts)) {
            return sprintf('%s %s', $starts->format('g:i A'), $starts->format('T'));
        }

        return $starts->isSameDay($ends)
            ? sprintf('%s – %s %s', $starts->format('g:i A'), $ends->format('g:i A'), $ends->format('T'))
            : sprintf('%s – %s %s', $starts->format('g:i A'), $ends->format('D, M j, g:i A'), $ends->format('T'));
    }

    /**
     * How long the occurrence lasts, or null for all-day and open-ended occurrences.
     */
    public function formatDuration(OccurrenceData $occurrence): ?string
    {
        if ($occurrence->allDay || $occurrence->ends->lessThanOrEqualTo($occurrence->starts)) {
            return null;
        }

        return $occurrence->starts->diff($occurrence->ends)->forHumans(['parts' => 2]);
    }

    /**
     * The external link entered for the event, ignoring the event page URL the model falls back to.
     */
    public function getExternalUrl(): ?string
    {
        $url = $this->getEvent()->getRawOriginal('url');

        return is_string($url) && filled($url) ? $url : null;
    }

    /**
     * @return ViewComponent[]
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('url')
                ->label('Open Link')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->color('gray')
                ->url(fn (): ?string => $this->getExternalUrl())
                ->openUrlInNewTab()
                ->visible(fn (): bool => filled($this->getExternalUrl())),
            TakeAttendanceAction::make(),
            CheckInLinkAction::make(),
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    /**
     * The page renders its own layout instead of filling the resource form, so it never needs
     * the read-only form a view page falls back to without an infolist.
     */
    protected function hasInfolist(): bool
    {
        return true;
    }
}
