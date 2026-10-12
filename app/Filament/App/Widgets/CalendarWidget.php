<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets;

use App\Data\Events\OccurrenceData;
use App\Filament\App\Resources\EventResource;
use App\Models\Event;
use App\Services\EventOccurrenceService;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\WeekDay;
use Filament\Actions\Action;
use Guava\Calendar\Filament\CalendarWidget as BaseCalendarWidget;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\HtmlString;

class CalendarWidget extends BaseCalendarWidget
{
    use HasWidgetShield;

    protected static ?int $sort = -1;

    protected string|HtmlString|null|bool $heading = 'Calendar';

    protected WeekDay $firstDay = WeekDay::Sunday;

    protected bool $useFilamentTimezone = true;

    protected bool $eventClickEnabled = true;

    protected int|string|array $columnSpan = 'full';

    protected ?string $defaultEventClickAction = 'view';

    public function authorize($ability, $arguments = []): Response
    {
        return Response::allow();
    }

    public function visitAction(): Action
    {
        return Action::make('visit')
            ->action(function (CalendarWidget $livewire): void {
                /** @var Event $record */
                $record = $livewire->getEventRecord();

                $this->redirect(EventResource::getUrl('view', [
                    'record' => $record,
                ]));
            });
    }

    /**
     * @return Collection|array<CalendarEvent>
     */
    protected function getEvents(array|FetchInfo $info = []): Collection|array
    {
        $calendarStart = Date::parse(data_get($info, 'start'));
        $calendarEnd = Date::parse(data_get($info, 'end'));

        return EventOccurrenceService::between($calendarStart, $calendarEnd)
            ->reject(fn (OccurrenceData $occurrence): bool => $occurrence->isCancelled())
            ->map(fn (OccurrenceData $occurrence): CalendarEvent => CalendarEvent::make()
                ->title($occurrence->event->name)
                ->start($occurrence->starts)
                ->end($occurrence->allDay && ! $occurrence->event->repeats ? ($occurrence->event->ends ?? $occurrence->event->starts) : $occurrence->ends)
                ->model(Event::class)
                ->key((string) $occurrence->event->getKey())
                ->action('visit')
                ->allDay($occurrence->allDay)
                ->backgroundColor($occurrence->event->calendar->color))
            ->values()
            ->all();
    }
}
