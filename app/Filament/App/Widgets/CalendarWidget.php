<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets;

use App\Filament\App\Resources\EventResource;
use App\Models\Event;
use App\Services\UserSettingsService;
use Closure;
use Filament\Actions\Action;
use Guava\Calendar\ValueObjects\Event as EventObject;
use Guava\Calendar\Widgets\CalendarWidget as BaseCalendarWidget;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class CalendarWidget extends BaseCalendarWidget
{
    protected static ?int $sort = -1;

    protected string|Closure|HtmlString|null $heading = '';

    protected bool $eventClickEnabled = true;

    protected int|string|array $columnSpan = 'full';

    protected ?string $defaultEventClickAction = 'view';

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

    public function getEvents(array $fetchInfo = []): Collection|array
    {
        $timezone = UserSettingsService::get('timezone', config('app.timezone'));

        return Event::all()->map(function (Event $event) use ($timezone) {
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
        });
    }
}
