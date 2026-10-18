<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets;

use App\Data\Events\OccurrenceData;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Event;
use App\Models\User;
use App\Services\EventCalendarService;
use App\Services\EventOccurrenceService;
use App\Services\UserSettingsService;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\WeekDay;
use Filament\Actions\Action;
use Guava\Calendar\Enums\CalendarViewType;
use Guava\Calendar\Filament\CalendarWidget as BaseCalendarWidget;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Guava\Calendar\ValueObjects\DateClickInfo;
use Guava\Calendar\ValueObjects\DateSelectInfo;
use Guava\Calendar\ValueObjects\EventClickInfo;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Locked;

class CalendarWidget extends BaseCalendarWidget
{
    use HasWidgetShield;

    /**
     * The fallback color for events whose calendar has none.
     */
    public const string DEFAULT_COLOR = '#2563eb';

    /**
     * The grid layout: "month" or "week".
     */
    public string $display = 'month';

    /**
     * @var array<int, int>
     */
    public array $calendarIds = [];

    public bool $mine = false;

    public bool $openOnly = false;

    /**
     * Whether the widget is rendered inside the Calendar page, which handles occurrence clicks and
     * quick-create selections itself.
     */
    #[Locked]
    public bool $onCalendarPage = false;

    protected static ?int $sort = -1;

    protected string|HtmlString|null|bool $heading = 'Calendar';

    protected WeekDay $firstDay = WeekDay::Sunday;

    protected bool $useFilamentTimezone = true;

    protected bool $eventClickEnabled = true;

    protected int|string|array $columnSpan = 'full';

    protected ?string $defaultEventClickAction = 'visit';

    /**
     * Whether cancelled occurrences are shown struck through rather than hidden.
     */
    protected bool $showCancelled = true;

    /**
     * Whether event pills reflect the signed-in member's RSVP.
     */
    protected bool $highlightResponses = true;

    public function authorize($ability, $arguments = []): Response
    {
        return Response::allow();
    }

    public function getHeading(): string|HtmlString|null
    {
        return $this->onCalendarPage ? null : parent::getHeading();
    }

    public function getCalendarView(): CalendarViewType
    {
        return $this->display === 'week' ? CalendarViewType::TimeGridWeek : CalendarViewType::DayGridMonth;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return [
            ...parent::getOptions(),
            'displayEventEnd' => false,
            'nowIndicator' => true,
            'scrollTime' => '08:00:00',
        ];
    }

    public function isDateClickEnabled(): bool
    {
        return $this->canQuickCreate();
    }

    public function isDateSelectEnabled(): bool
    {
        return $this->canQuickCreate();
    }

    public function visitAction(): Action
    {
        return Action::make('visit')
            ->action(function (array $arguments): void {
                $event = $this->getEventRecord();
                $starts = data_get($arguments, 'data.event.extendedProps.starts');

                if (! $event instanceof Event) {
                    return;
                }

                $occurrence = is_numeric($starts) ? EventOccurrenceService::find($event, Date::createFromTimestamp((int) $starts, config('app.timezone'))) : null;

                $this->redirect($occurrence?->url() ?? $event->url);
            });
    }

    protected function onEventClick(EventClickInfo $info, Model $event, ?string $action = null): void
    {
        if (! $this->onCalendarPage) {
            parent::onEventClick($info, $event, $action);

            return;
        }

        $starts = $this->getRawCalendarContextData('event.extendedProps.starts');

        if (! is_numeric($starts)) {
            return;
        }

        $this->dispatch('calendar-occurrence-selected', eventId: $event->getKey(), starts: (int) $starts);
    }

    protected function onDateClick(DateClickInfo $info): void
    {
        if ($info->allDay) {
            $this->dispatch('calendar-range-selected', starts: $info->date->toDateString(), ends: $info->date->toDateString(), allDay: true);

            return;
        }

        $starts = $this->wallClockToUtc($info->date->format('Y-m-d H:i:s'));

        $this->dispatch('calendar-range-selected', starts: $starts->toDateTimeString(), ends: $starts->copy()->addHour()->toDateTimeString(), allDay: false);
    }

    protected function onDateSelect(DateSelectInfo $info): void
    {
        // All-day selections end at midnight after the last selected day.
        if ($info->allDay) {
            $this->dispatch('calendar-range-selected', starts: $info->start->toDateString(), ends: $info->end->subDay()->toDateString(), allDay: true);

            return;
        }

        $starts = $this->wallClockToUtc($info->start->format('Y-m-d H:i:s'));
        $ends = $this->wallClockToUtc($info->end->format('Y-m-d H:i:s'));

        $this->dispatch('calendar-range-selected', starts: $starts->toDateTimeString(), ends: $ends->toDateTimeString(), allDay: false);
    }

    /**
     * @return Collection|array<CalendarEvent>
     */
    protected function getEvents(array|FetchInfo $info = []): Collection|array
    {
        $calendarStart = Date::parse(data_get($info, 'start'));
        $calendarEnd = Date::parse(data_get($info, 'end'));
        $user = Auth::user();
        $user = $user instanceof User ? $user : null;

        $occurrences = EventCalendarService::occurrences($calendarStart, $calendarEnd, [
            'calendars' => $this->calendarIds,
            'mine' => $this->mine,
            'open' => $this->openOnly,
        ], $user, $this->showCancelled);

        $responses = $this->highlightResponses ? EventCalendarService::responses($occurrences, $user) : collect();
        $timezone = UserSettingsService::timezone();

        return $occurrences
            ->map(fn (OccurrenceData $occurrence): CalendarEvent => $this->toCalendarEvent($occurrence, $responses->get($occurrence->key()), $timezone))
            ->values()
            ->all();
    }

    protected function toCalendarEvent(OccurrenceData $occurrence, ?EventRegistrationStatus $response, string $timezone): CalendarEvent
    {
        $color = $occurrence->event->calendar?->color ?: static::DEFAULT_COLOR;
        $cancelled = $occurrence->isCancelled();
        $filled = ! $this->highlightResponses || in_array($response, [EventRegistrationStatus::GOING, EventRegistrationStatus::ATTENDED], true);

        $styles = collect([
            'box-shadow' => $filled ? null : sprintf('inset 0 0 0 1.5px %s', $color),
            'text-decoration' => $cancelled ? 'line-through' : null,
            'opacity' => $cancelled ? '0.55' : null,
        ])->filter()->all();

        return CalendarEvent::make()
            ->title($occurrence->event->name)
            ->start($occurrence->allDay ? $occurrence->localStarts() : $occurrence->starts)
            ->end($occurrence->allDay ? $occurrence->localEnds()->startOfDay()->addDay() : $occurrence->ends)
            ->timezone($timezone)
            ->model(Event::class)
            ->key((string) $occurrence->event->getKey())
            ->action('visit')
            ->allDay($occurrence->allDay)
            ->backgroundColor($filled ? $color : sprintf('color-mix(in srgb, %s 14%%, transparent)', $color))
            ->textColor($filled ? '#ffffff' : $color)
            ->styles($styles)
            ->extendedProps([
                'starts' => $occurrence->starts->getTimestamp(),
                'color' => $color,
                'cancelled' => $cancelled,
                'response' => $response?->value,
            ]);
    }

    protected function canQuickCreate(): bool
    {
        return $this->onCalendarPage && (Auth::user()?->can('create', Event::class) ?? false);
    }

    /**
     * The calendar renders in the member's timezone, so a clicked wall-clock time is local to them.
     */
    protected function wallClockToUtc(string $wallClock): Carbon
    {
        return Date::parse($wallClock, UserSettingsService::timezone())->setTimezone(config('app.timezone'));
    }
}
