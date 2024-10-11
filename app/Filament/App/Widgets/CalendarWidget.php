<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets;

use App\Filament\App\Resources\EventResource;
use App\Models\Event;
use App\Services\RepeatService;
use App\Services\UserSettingsService;
use Carbon\CarbonInterface;
use Closure;
use Filament\Actions\Action;
use Guava\Calendar\ValueObjects\Event as EventObject;
use Guava\Calendar\Widgets\CalendarWidget as BaseCalendarWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class CalendarWidget extends BaseCalendarWidget
{
    protected static ?int $sort = -1;

    protected string|Closure|HtmlString|null $heading = '';

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
            ->where(function (Builder $query) use ($calendarStart, $calendarEnd) {
                $query
                    ->whereBetween('starts', [$calendarStart, $calendarEnd])
                    ->orWhereBetween('ends', [$calendarStart, $calendarEnd])
                    ->orWhere(function (Builder $query) use ($calendarStart, $calendarEnd) {
                        $query
                            ->whereDate('starts', '<=', $calendarStart)
                            ->whereDate('ends', '>=', $calendarEnd);
                    });
            })
            ->orWhere(function (Builder $query) use ($calendarStart, $calendarEnd) {
                $query->whereHas('schedule', function (Builder $query) use ($calendarStart, $calendarEnd) {
                    $query->whereDate('start', '<=', $calendarEnd)
                        ->orWhere(function (Builder $query) use ($calendarStart) {
                            $query->whereNotNull('until')
                                ->whereDate('until', '>=', $calendarStart);
                        });
                });
            });
    }

    public function authorize($ability, $arguments = []): true
    {
        return true;
    }

    public function visitAction(): Action
    {
        return Action::make('visit')
            ->action(function (CalendarWidget $livewire) {
                /** @var Event $record */
                $record = $livewire->getEventRecord();

                $this->redirect(EventResource::getUrl('view', [
                    'record' => $record,
                ]));
            });
    }

    /**
     * @return Collection|array<EventObject>
     */
    public function getEvents(array $fetchInfo = []): Collection|array
    {
        $timezone = UserSettingsService::get('timezone', config('app.timezone'));

        $calendarStart = Carbon::parse(data_get($fetchInfo, 'start'));
        $calendarEnd = Carbon::parse(data_get($fetchInfo, 'end'));

        $this->events = CalendarWidget::query($calendarStart, $calendarEnd)->get();

        return array_merge($this->getOneTimeEvents($timezone), $this->getRepeatingEvents($timezone, $calendarStart, $calendarEnd));
    }

    /**
     * @return array<EventObject>
     */
    private function getRepeatingEvents(string $timezone, CarbonInterface $calendarStart, CarbonInterface $calendarEnd): array
    {
        if (! $this->events instanceof Collection) {
            return [];
        }

        return $this->events
            ->filter(function (Event $event) {
                return $event->repeats && filled($event->schedule);
            })
            ->flatMap(function (Event $event) use ($timezone, $calendarStart, $calendarEnd) {
                return collect(RepeatService::occurrenceBetween($event->schedule, $calendarStart, $calendarEnd))->map(function (Carbon $occurrence) use ($timezone, $event) {
                    $start = $occurrence->setTimezone($timezone)->shiftTimezone('UTC');
                    $end = $start->copy()->addHours($event->schedule->duration);

                    return EventObject::make()
                        ->title($event->name)
                        ->start($start)
                        ->end($end)
                        ->model(Event::class)
                        ->key((string) $event->getKey())
                        ->action('visit')
                        ->allDay($event->all_day)
                        ->backgroundColor($event->calendar->color);
                });
            })->toArray();
    }

    /**
     * @return array<EventObject>
     */
    private function getOneTimeEvents(string $timezone): array
    {
        if (! $this->events instanceof Collection) {
            return [];
        }

        return $this->events->reject->repeats->map(function (Event $event) use ($timezone) {
            $start = $event->starts->setTimezone($timezone)->shiftTimezone('UTC');
            $end = $event->ends->setTimezone($timezone)->shiftTimezone('UTC');

            return EventObject::make()
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
