<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Data\Events\OccurrenceData;
use App\Filament\App\Resources\EventResource;
use App\Livewire\Concerns\RespondsToEvents;
use App\Models\Calendar as CalendarModel;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use App\Services\EventCalendarService;
use App\Services\EventOccurrenceService;
use App\Services\EventRegistrationService;
use App\Services\UserSettingsService;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Carbon\CarbonInterface;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

class Calendar extends Page
{
    use HasPageShield;
    use RespondsToEvents;

    /**
     * The calendar layouts a member can switch between.
     */
    public const array DISPLAYS = ['agenda', 'month', 'week'];

    /**
     * The number of days the agenda loads at a time.
     */
    public const int AGENDA_WINDOW_DAYS = 30;

    public string $display = 'month';

    /**
     * Whether the member has chosen a layout before. Without one, small screens open on the agenda.
     */
    public bool $hasDisplayPreference = false;

    /**
     * @var array<int, int>
     */
    #[Url(as: 'calendars', except: [])]
    public array $calendarIds = [];

    #[Url(except: false)]
    public bool $mine = false;

    #[Url(as: 'open', except: false)]
    public bool $openOnly = false;

    #[Url(except: false)]
    public bool $past = false;

    public int $agendaWindows = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static ?int $navigationSort = 2;

    protected ?string $subheading = "Keep up-to-date with your organization's calendar of events and schedule.";

    /**
     * @var array<string, OccurrenceData>
     */
    protected array $resolvedOccurrences = [];

    /**
     * The heading shown above a day in the agenda.
     */
    public static function dayHeading(string $date): string
    {
        $day = Carbon::parse($date, UserSettingsService::timezone());
        $today = now(UserSettingsService::timezone())->startOfDay();

        return match (true) {
            $day->isSameDay($today) => 'Today',
            $day->isSameDay($today->copy()->addDay()) => 'Tomorrow',
            $day->isSameDay($today->copy()->subDay()) => 'Yesterday',
            $day->year === $today->year => $day->format('l, F j'),
            default => $day->format('l, F j, Y'),
        };
    }

    public function mount(): void
    {
        $preference = UserSettingsService::get('calendar_view');

        $this->hasDisplayPreference = in_array($preference, static::DISPLAYS, true);
        $this->display = $this->hasDisplayPreference ? $preference : 'month';
        $this->calendarIds = $this->normalizeCalendarIds($this->calendarIds);
    }

    public function getView(): string
    {
        return 'filament.app.pages.calendar';
    }

    public function setDisplay(string $display, bool $remember = true): void
    {
        if (! in_array($display, static::DISPLAYS, true)) {
            return;
        }

        $this->display = $display;

        if ($remember) {
            UserSettingsService::save('calendar_view', $display);
            $this->hasDisplayPreference = true;
        }
    }

    public function toggleCalendar(int $calendarId): void
    {
        $selected = collect($this->calendarIds);

        $this->calendarIds = $this->normalizeCalendarIds($selected->contains($calendarId)
            ? $selected->reject(fn (int $id): bool => $id === $calendarId)->all()
            : $selected->push($calendarId)->all());

        $this->agendaWindows = 1;
    }

    public function toggleMine(): void
    {
        $this->mine = ! $this->mine;
        $this->agendaWindows = 1;
    }

    public function toggleOpenOnly(): void
    {
        $this->openOnly = ! $this->openOnly;
        $this->agendaWindows = 1;
    }

    public function togglePast(): void
    {
        $this->past = ! $this->past;
        $this->agendaWindows = 1;
    }

    public function clearFilters(): void
    {
        $this->reset(['calendarIds', 'mine', 'openOnly', 'agendaWindows']);
    }

    public function loadMore(): void
    {
        $this->agendaWindows++;
    }

    #[On('calendar-occurrence-selected')]
    public function selectOccurrence(int $eventId, int $starts): void
    {
        $this->mountAction('occurrence', ['event' => $eventId, 'starts' => $starts]);
    }

    #[On('calendar-range-selected')]
    public function selectRange(string $starts, string $ends, bool $allDay = false): void
    {
        $this->mountAction('quickCreate', ['starts' => $starts, 'ends' => $ends, 'allDay' => $allDay]);
    }

    #[On('event-response-updated')]
    public function refreshCalendar(): void
    {
        $this->dispatch('calendar--refresh');
    }

    public function isFiltering(): bool
    {
        return filled($this->calendarIds) || $this->mine || $this->openOnly;
    }

    /**
     * @return array{calendars: array<int, int>, mine: bool, open: bool}
     */
    public function filters(): array
    {
        return [
            'calendars' => $this->calendarIds,
            'mine' => $this->mine,
            'open' => $this->openOnly,
        ];
    }

    /**
     * The date range the agenda currently covers.
     *
     * @return array{0: CarbonInterface, 1: CarbonInterface}
     */
    public function agendaRange(): array
    {
        $days = static::AGENDA_WINDOW_DAYS * max(1, $this->agendaWindows);
        $today = now(UserSettingsService::timezone())->startOfDay();

        return $this->past
            ? [$today->copy()->subDays($days)->utc(), now()]
            : [$today->copy()->utc(), $today->copy()->addDays($days)->endOfDay()->utc()];
    }

    public function occurrenceAction(): Action
    {
        return Action::make('occurrence')
            ->modalHeading(fn (array $arguments): string => $this->resolveOccurrence($arguments)->event->name)
            ->modalDescription(fn (array $arguments): string => $this->describeWhen($this->resolveOccurrence($arguments)))
            ->modalWidth(Width::Large)
            ->modalContent(fn (array $arguments): View => view('filament.app.pages.calendar.occurrence', $this->occurrenceDetails($this->resolveOccurrence($arguments))))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close')
            ->extraModalFooterActions(fn (array $arguments): array => [
                Action::make('openEvent')
                    ->label('Open Event')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url($this->resolveOccurrence($arguments)->url()),
                $this->makeCancelOccurrenceAction($arguments),
                $this->makeRestoreOccurrenceAction($arguments),
            ]);
    }

    public function quickCreateAction(): Action
    {
        $timezone = UserSettingsService::timezone();

        return Action::make('quickCreate')
            ->label('New Event')
            ->icon(Heroicon::OutlinedPlus)
            ->visible(fn (): bool => Auth::user()?->can('create', Event::class) ?? false)
            ->slideOver()
            ->modalHeading('New Event')
            ->modalWidth(Width::Medium)
            ->modalSubmitActionLabel('Create')
            ->fillForm(fn (array $arguments): array => [
                'calendar_id' => collect($this->calendarIds)->first() ?? CalendarModel::query()->orderBy('name')->value('id'),
                'all_day' => (bool) ($arguments['allDay'] ?? false),
                'starts' => $arguments['starts'] ?? now()->addHour()->startOfHour()->toDateTimeString(),
                'ends' => $arguments['ends'] ?? now()->addHours(2)->startOfHour()->toDateTimeString(),
            ])
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),
                Select::make('calendar_id')
                    ->label('Calendar')
                    ->options(fn (): Collection => CalendarModel::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Toggle::make('all_day')
                    ->live(),
                DateTimePicker::make('starts')
                    ->timezone($timezone)
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, Get $get, mixed $state): void {
                        if (blank($state)) {
                            return;
                        }

                        if ($get('all_day')) {
                            $set('ends', $state);
                        } elseif (Date::parse($state)->isAfter(Date::parse($get('ends') ?? $state))) {
                            $set('ends', Date::parse($state)->addHour()->toDateTimeString());
                        }
                    })
                    ->time(fn (Get $get): bool => ! $get('all_day')),
                DateTimePicker::make('ends')
                    ->timezone($timezone)
                    ->required()
                    ->afterOrEqual('starts')
                    ->time(fn (Get $get): bool => ! $get('all_day')),
            ])
            ->extraModalFooterActions(fn (Action $action): array => [
                $action->makeModalSubmitAction('moreOptions', arguments: ['more' => true])
                    ->label('More Options')
                    ->color('gray'),
            ])
            ->action(function (array $data, array $arguments): void {
                abort_unless(Auth::user()?->can('create', Event::class), 403);

                if ($arguments['more'] ?? false) {
                    $this->redirect(EventResource::getUrl('create', collect($data)
                        ->only(['name', 'calendar_id', 'starts', 'ends'])
                        ->put('all_day', (int) (bool) ($data['all_day'] ?? false))
                        ->all()));

                    return;
                }

                $event = Event::query()->create([
                    'all_day' => (bool) ($data['all_day'] ?? false),
                    'name' => $data['name'],
                    'calendar_id' => $data['calendar_id'],
                    'starts' => $data['starts'],
                    'ends' => $data['ends'],
                    'author_id' => Auth::id(),
                ]);

                Notification::make()
                    ->success()
                    ->title('Event created.')
                    ->actions([
                        Action::make('view')
                            ->label('Open event')
                            ->button()
                            ->url(EventResource::getUrl('view', ['record' => $event])),
                    ])
                    ->send();

                $this->dispatch('calendar--refresh');
            });
    }

    /**
     * Resolve the occurrence an action was mounted for, making sure the member can see its event.
     *
     * @param  array<string, mixed>  $arguments
     */
    public function resolveOccurrence(array $arguments, bool $fresh = false): OccurrenceData
    {
        $cacheKey = sprintf('%s|%s', $arguments['event'] ?? '', $arguments['starts'] ?? '');

        if (! $fresh && isset($this->resolvedOccurrences[$cacheKey])) {
            return $this->resolvedOccurrences[$cacheKey];
        }

        $user = Auth::user();
        $event = Event::query()->find($arguments['event'] ?? null);

        abort_unless($user instanceof User && $event instanceof Event && $user->can('view', $event), 403);
        abort_unless(is_numeric($arguments['starts'] ?? null), 404);

        $occurrence = EventOccurrenceService::find($event, Date::createFromTimestamp((int) $arguments['starts'], config('app.timezone')));

        abort_unless($occurrence instanceof OccurrenceData, 404);

        return $this->resolvedOccurrences[$cacheKey] = $occurrence;
    }

    public function canManageDate(OccurrenceData $occurrence): bool
    {
        return EventCalendarService::canCancelDate($occurrence)
            && (Auth::user()?->can('update', $occurrence->event) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $calendars = CalendarModel::query()->orderBy('name')->get();

        if ($this->display !== 'agenda') {
            return [
                'calendars' => $calendars,
                'days' => collect(),
                'responses' => collect(),
            ];
        }

        [$start, $end] = $this->agendaRange();
        $user = Auth::user();
        $user = $user instanceof User ? $user : null;

        $occurrences = EventCalendarService::occurrences($start, $end, $this->filters(), $user)
            ->filter(fn (OccurrenceData $occurrence): bool => ! $this->past || $occurrence->starts->lessThan(now()));

        if ($this->past) {
            $occurrences = $occurrences->sortByDesc(fn (OccurrenceData $occurrence): int => $occurrence->starts->getTimestamp())->values();
        }

        return [
            'calendars' => $calendars,
            'days' => $occurrences->groupBy(fn (OccurrenceData $occurrence): string => $occurrence->localStarts()->toDateString()),
            'responses' => EventCalendarService::responses($occurrences, $user),
        ];
    }

    /**
     * @return array<int, Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            $this->quickCreateAction(),
        ];
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    protected function makeCancelOccurrenceAction(array $arguments): Action
    {
        $occurrence = $this->resolveOccurrence($arguments);

        return Action::make('cancelOccurrence')
            ->label('Cancel This Date')
            ->icon(Heroicon::OutlinedNoSymbol)
            ->color('danger')
            ->visible($this->canManageDate($occurrence) && ! $occurrence->isCancelled())
            ->requiresConfirmation()
            ->modalHeading('Cancel This Date')
            ->modalDescription('Only this date is cancelled. Members who are going or might attend will be notified.')
            ->modalSubmitActionLabel('Cancel Date')
            ->schema([
                Textarea::make('reason')
                    ->helperText('Optional. Shared with members in the cancellation notice.')
                    ->maxLength(1000),
            ])
            ->action(function (array $data) use ($arguments): void {
                $occurrence = $this->resolveOccurrence($arguments, fresh: true);

                abort_unless($this->canManageDate($occurrence), 403);

                EventCalendarService::cancel($occurrence, $data['reason'] ?? null);

                $this->resolvedOccurrences = [];

                Notification::make()
                    ->success()
                    ->title('This date has been cancelled.')
                    ->send();

                $this->dispatch('calendar--refresh');
            });
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    protected function makeRestoreOccurrenceAction(array $arguments): Action
    {
        $occurrence = $this->resolveOccurrence($arguments);

        return Action::make('restoreOccurrence')
            ->label('Restore This Date')
            ->icon(Heroicon::OutlinedArrowUturnLeft)
            ->color('gray')
            ->visible($this->canManageDate($occurrence) && $occurrence->isCancelled())
            ->requiresConfirmation()
            ->modalHeading('Restore This Date')
            ->modalDescription('This date will be put back on the calendar.')
            ->modalSubmitActionLabel('Restore')
            ->action(function () use ($arguments): void {
                $occurrence = $this->resolveOccurrence($arguments, fresh: true);

                abort_unless($this->canManageDate($occurrence), 403);

                EventCalendarService::restore($occurrence);

                $this->resolvedOccurrences = [];

                Notification::make()
                    ->success()
                    ->title('This date has been restored.')
                    ->send();

                $this->dispatch('calendar--refresh');
            });
    }

    /**
     * @return array<string, mixed>
     */
    protected function occurrenceDetails(OccurrenceData $occurrence): array
    {
        $user = Auth::user();
        $responses = EventRegistrationService::responses($occurrence);

        /** @var Collection<int, EventRegistration> $going */
        $going = collect($responses->get(EventRegistrationStatus::GOING->value, []))
            ->merge($responses->get(EventRegistrationStatus::ATTENDED->value, []));

        return [
            'occurrence' => $occurrence,
            'status' => $user instanceof User ? EventRegistrationService::responseFor($occurrence, $user) : null,
            'going' => $going,
            'summary' => EventRegistrationService::summary($occurrence),
        ];
    }

    protected function describeWhen(OccurrenceData $occurrence): string
    {
        $starts = $occurrence->localStarts();
        $ends = $occurrence->localEnds();

        if ($occurrence->allDay) {
            return sprintf('%s · All day', $starts->format('l, F j, Y'));
        }

        return sprintf('%s · %s – %s', $starts->format('l, F j, Y'), $starts->format('g:i A'), $ends->format($ends->isSameDay($starts) ? 'g:i A' : 'M j, g:i A'));
    }

    /**
     * @param  array<int, int|string>  $calendarIds
     * @return array<int, int>
     */
    protected function normalizeCalendarIds(array $calendarIds): array
    {
        return collect($calendarIds)
            ->map(fn (int|string $id): int => (int) $id)
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->sort()
            ->values()
            ->all();
    }
}
