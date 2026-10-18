<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\EventResource\Actions;

use App\Data\Events\OccurrenceData;
use App\Filament\App\Resources\EventResource\Pages\ViewEvent;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use App\Services\EventAttendanceService;
use App\Services\EventOccurrenceService;
use App\Services\ScheduleService;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

class TakeAttendanceAction extends Action
{
    /**
     * How far back past occurrences of a repeating event can have attendance taken.
     */
    public const int OCCURRENCE_LOOKBACK_DAYS = 90;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Take Attendance');
        $this->icon(Heroicon::OutlinedClipboardDocumentCheck);
        $this->color('gray');

        $this->modalHeading('Take Attendance');
        $this->modalDescription('Check off the members who attended the event. Attendance can trigger automations and, if enabled, create training records.');
        $this->modalSubmitActionLabel('Save Attendance');

        $this->successNotificationTitle('Attendance has been saved.');
        $this->authorize('update');

        $this->fillForm(function (Event $record): array {
            $default = static::defaultOccurrenceOption($record);
            $occurrence = static::occurrenceFor($record, $default);

            return [
                'occurrence' => $default,
                'attended' => static::attendedUserIds($occurrence),
                'mark_no_show' => true,
            ];
        });

        $this->schema([
            Select::make('occurrence')
                ->label('Occurrence')
                ->helperText('Select the date you are taking attendance for.')
                ->options(fn (Event $record): array => static::occurrenceOptions($record))
                ->visible(fn (Event $record): bool => $record->repeats)
                ->required(fn (Event $record): bool => $record->repeats)
                ->selectablePlaceholder(false)
                ->live()
                ->afterStateUpdated(fn (Event $record, Set $set, ?string $state): mixed => $set('attended', static::attendedUserIds(static::occurrenceFor($record, $state)))),
            CheckboxList::make('attended')
                ->label('Registered members')
                ->helperText('Select the registered members who attended.')
                ->options(fn (Event $record, Get $get): array => User::query()
                    ->whereKey(static::registrants(static::occurrenceFor($record, $get('occurrence')))->keys()->all())
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all())
                ->visible(fn (Event $record, Get $get): bool => static::registrants(static::occurrenceFor($record, $get('occurrence')))->isNotEmpty())
                ->bulkToggleable()
                ->searchable()
                ->columns(2),
            Select::make('additional')
                ->label('Additional attendees')
                ->helperText('Add members who attended without registering.')
                ->multiple()
                ->searchable()
                ->preload()
                ->options(fn (Event $record, Get $get): array => User::query()
                    ->whereKeyNot(static::registrants(static::occurrenceFor($record, $get('occurrence')))->keys()->all())
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all()),
            Toggle::make('mark_no_show')
                ->label('Mark unchecked registrants as no-show')
                ->helperText("Only members who responded Going are marked. Members who responded Maybe or Can't Go are left unchanged.")
                ->visible(fn (Event $record, Get $get): bool => static::registrants(static::occurrenceFor($record, $get('occurrence')))->isNotEmpty()),
        ]);

        $this->action(function (Event $record, Action $action, array $data): void {
            $occurrence = static::occurrenceFor($record, data_get($data, 'occurrence'));

            $attended = collect(data_get($data, 'attended', []))
                ->merge(data_get($data, 'additional', []))
                ->map(fn (mixed $id): int => (int) $id)
                ->unique()
                ->values()
                ->all();

            $noShow = data_get($data, 'mark_no_show', false)
                ? static::registrants($occurrence)
                    ->filter(fn (EventRegistration $registration): bool => in_array($registration->status, static::noShowEligibleStatuses(), true))
                    ->keys()
                    ->diff($attended)
                    ->values()
                    ->all()
                : [];

            EventAttendanceService::recordAttendance($record, $attended, $noShow, $occurrence);

            $this->dispatchTo(ViewEvent::class, 'refreshRegistrations');
            $action->success();
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'takeAttendance';
    }

    /**
     * @return array<string, string>
     */
    protected static function occurrenceOptions(Event $record): array
    {
        if (! $record->repeats) {
            return [];
        }

        return EventOccurrenceService::forEvent($record, now()->subDays(static::OCCURRENCE_LOOKBACK_DAYS), now()->addDay())
            ->reject(fn (OccurrenceData $occurrence): bool => $occurrence->isCancelled())
            ->sortByDesc(fn (OccurrenceData $occurrence): int => $occurrence->starts->getTimestamp())
            ->mapWithKeys(fn (OccurrenceData $occurrence): array => [
                $occurrence->starts->toIso8601String() => $occurrence->starts->copy()->setTimezone(ScheduleService::timezone())->toDayDateTimeString(),
            ])
            ->all();
    }

    /**
     * The most recent occurrence option that has already started, falling back to the latest option.
     */
    protected static function defaultOccurrenceOption(Event $record): ?string
    {
        $options = array_keys(static::occurrenceOptions($record));

        return collect($options)->first(fn (string $starts): bool => Date::parse($starts)->lessThanOrEqualTo(now())) ?? collect($options)->first();
    }

    /**
     * The responses that mean a member committed to attending, so an unchecked member can be marked
     * as a no-show. Members already marked attended or no-show keep being corrected on later saves.
     *
     * @return array<int, EventRegistrationStatus>
     */
    protected static function noShowEligibleStatuses(): array
    {
        return [
            EventRegistrationStatus::GOING,
            EventRegistrationStatus::ATTENDED,
            EventRegistrationStatus::NO_SHOW,
        ];
    }

    protected static function occurrenceFor(Event $record, ?string $starts): ?OccurrenceData
    {
        if (! $record->repeats || blank($starts)) {
            return EventOccurrenceService::nearest($record);
        }

        return EventOccurrenceService::find($record, Date::parse($starts)->setTimezone(config('app.timezone')));
    }

    /**
     * @return Collection<int, EventRegistration>
     */
    protected static function registrants(?OccurrenceData $occurrence): Collection
    {
        return $occurrence instanceof OccurrenceData ? EventAttendanceService::registrationsFor($occurrence) : collect();
    }

    /**
     * @return array<int, int>
     */
    protected static function attendedUserIds(?OccurrenceData $occurrence): array
    {
        return static::registrants($occurrence)
            ->filter(fn (EventRegistration $registration): bool => $registration->status === EventRegistrationStatus::ATTENDED)
            ->keys()
            ->all();
    }
}
