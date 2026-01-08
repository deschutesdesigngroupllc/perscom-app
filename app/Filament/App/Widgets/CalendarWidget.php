<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets;

use App\Filament\App\Resources\EventResource;
use App\Models\Event;
use App\Services\ScheduleService;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\CarbonInterface;
use Carbon\WeekDay;
use Closure;
use Filament\Actions\Action;
use Guava\Calendar\Filament\CalendarWidget as BaseCalendarWidget;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
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

    protected Collection|Closure|array $events;

    /**
     * @return Builder<Event>
     */
    public static function query(CarbonInterface $calendarStart, CarbonInterface $calendarEnd): Builder
    {
        return Event::query()
            ->where(function (Builder $query) use ($calendarStart, $calendarEnd): void {
                $query
                    ->whereBetween('starts', [$calendarStart, $calendarEnd])
                    ->orWhereBetween('ends', [$calendarStart, $calendarEnd])
                    ->orWhere(function (Builder $query) use ($calendarStart, $calendarEnd): void {
                        $query
                            ->whereDate('starts', '<=', $calendarStart)
                            ->whereDate('ends', '>=', $calendarEnd);
                    });
            })
            ->orWhere(function (Builder $query) use ($calendarStart, $calendarEnd): void {
                $query->whereHas('schedule', function (Builder $query) use ($calendarStart, $calendarEnd): void {
                    $query->whereDate('start', '<=', $calendarEnd)
                        ->orWhere(function (Builder $query) use ($calendarStart): void {
                            $query->whereNotNull('until')
                                ->whereDate('until', '>=', $calendarStart);
                        });
                });
            });
    }

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
        $calendarStart = Carbon::parse(data_get($info, 'start'));
        $calendarEnd = Carbon::parse(data_get($info, 'end'));

        $this->events = CalendarWidget::query($calendarStart, $calendarEnd)->get();

        return array_merge($this->getOneTimeEvents(), $this->getRepeatingEvents($calendarStart, $calendarEnd));
    }

    /**
     * @return array<CalendarEvent>
     */
    private function getRepeatingEvents(CarbonInterface $calendarStart, CarbonInterface $calendarEnd): array
    {
        if (! $this->events instanceof Collection) {
            return [];
        }

        return $this->events
            ->filter(fn (Event $event): bool => $event->repeats && filled($event->schedule))
            ->flatMap(fn (Event $event) => collect(ScheduleService::occurrenceBetween($event->schedule, $calendarStart, $calendarEnd))->map(function (Carbon $occurrence) use ($event): CalendarEvent {
                $start = $occurrence;
                $end = $start->copy()->addHours($event->schedule->duration);

                return CalendarEvent::make()
                    ->title($event->name)
                    ->start($start)
                    ->end($end)
                    ->model(Event::class)
                    ->key((string) $event->getKey())
                    ->action('visit')
                    ->allDay($event->all_day)
                    ->backgroundColor($event->calendar->color);
            }))->toArray();
    }

    /**
     * @return array<CalendarEvent>
     */
    private function getOneTimeEvents(): array
    {
        if (! $this->events instanceof Collection) {
            return [];
        }

        return $this->events->reject->repeats->map(function (Event $event): CalendarEvent {
            $start = $event->starts;
            $end = $event->ends;

            return CalendarEvent::make()
                ->title($event->name)
                ->start($start)
                ->end($end)
                ->model(Event::class)
                ->key((string) $event->getKey())
                ->action('visit')
                ->allDay($event->all_day)
                ->backgroundColor($event->calendar->color);
        })->toArray();
    }
}
