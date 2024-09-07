<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets;

use App\Filament\App\Resources\EventResource;
use App\Models\Event;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
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
        return Event::all()->map(function (Event $event) {
            return EventObject::make()
                ->title($event->name)
                ->start($event->start)
                ->end($event->end)
                ->model(Event::class)
                ->key((string) $event->getKey())
                ->action('visit')
                ->allDay($event->all_day)
                ->backgroundColor($event->calendar->color);
        });
    }

    public function getDateClickContextMenuActions(): array
    {
        return [
            CreateAction::make('foo')
                ->model(Event::class)
                ->form([
                    TextInput::make('name'),
                    DatePicker::make('starts'),
                    DatePicker::make('ends'),
                ])
                ->mountUsing(fn ($arguments, $form) => $form->fill([
                    'starts' => data_get($arguments, 'dateStr'),
                    'ends' => data_get($arguments, 'dateStr'),
                ])),
        ];
    }
}
