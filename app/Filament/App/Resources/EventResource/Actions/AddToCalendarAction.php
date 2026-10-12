<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\EventResource\Actions;

use App\Data\Events\OccurrenceData;
use App\Models\Event;
use App\Services\CalendarFeedService;
use App\Services\EventOccurrenceService;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Support\Icons\Heroicon;

/**
 * "Add to Calendar" menu for an event occurrence: download an .ics file or open a pre-filled
 * Google Calendar event. Defaults to the occurrence nearest to now.
 */
class AddToCalendarAction extends ActionGroup
{
    protected OccurrenceData|Closure|null $occurrence = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Add to Calendar');
        $this->icon(Heroicon::OutlinedCalendarDays);
        $this->color('gray');
        $this->button();

        $this->actions([
            Action::make('downloadIcs')
                ->label('Download .ics')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->url(fn (Event $record): string => route('app.events.calendar', array_filter([
                    'event' => $record->getKey(),
                    'occurrence' => $this->resolveOccurrence($record)?->starts->getTimestamp(),
                ]))),
            Action::make('googleCalendar')
                ->label('Google Calendar')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->visible(fn (Event $record): bool => $this->resolveOccurrence($record) instanceof OccurrenceData)
                ->url(fn (Event $record): ?string => ($occurrence = $this->resolveOccurrence($record)) instanceof OccurrenceData
                    ? CalendarFeedService::googleTemplateUrl($occurrence)
                    : null, shouldOpenInNewTab: true),
        ]);
    }

    /**
     * @param  array<Action|ActionGroup>  $actions
     */
    public static function make(array $actions = []): static
    {
        return parent::make($actions);
    }

    /**
     * The occurrence to add. Accepts an OccurrenceData or a closure receiving the event record.
     */
    public function occurrence(OccurrenceData|Closure|null $occurrence): static
    {
        $this->occurrence = $occurrence;

        return $this;
    }

    public function resolveOccurrence(Event $record): ?OccurrenceData
    {
        $occurrence = $this->evaluate($this->occurrence, ['record' => $record], [Event::class => $record]);

        return $occurrence instanceof OccurrenceData ? $occurrence : EventOccurrenceService::nearest($record);
    }
}
