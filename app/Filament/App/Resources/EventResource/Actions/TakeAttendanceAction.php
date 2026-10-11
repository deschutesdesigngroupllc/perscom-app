<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\EventResource\Actions;

use App\Filament\App\Resources\EventResource\Pages\ViewEvent;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Event;
use App\Models\User;
use App\Services\EventAttendanceService;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Support\Icons\Heroicon;

class TakeAttendanceAction extends Action
{
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

        $this->fillForm(fn (Event $record): array => [
            'attended' => $record->registrations
                ->filter(fn (User $user): bool => $user->registration?->status === EventRegistrationStatus::ATTENDED)
                ->modelKeys(),
            'mark_no_show' => true,
        ]);

        $this->schema([
            CheckboxList::make('attended')
                ->label('Registered members')
                ->helperText('Select the registered members who attended.')
                ->options(fn (Event $record): array => $record->registrations
                    ->mapWithKeys(fn (User $user): array => [$user->getKey() => $user->name])
                    ->all())
                ->visible(fn (Event $record): bool => $record->registrations->isNotEmpty())
                ->bulkToggleable()
                ->searchable()
                ->columns(2),
            Select::make('additional')
                ->label('Additional attendees')
                ->helperText('Add members who attended without registering.')
                ->multiple()
                ->searchable()
                ->preload()
                ->options(fn (Event $record): array => User::query()
                    ->whereKeyNot($record->registrations->modelKeys())
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all()),
            Toggle::make('mark_no_show')
                ->label('Mark unchecked registrants as no-show')
                ->visible(fn (Event $record): bool => $record->registrations->isNotEmpty()),
        ]);

        $this->action(function (Event $record, Action $action, array $data): void {
            $attended = collect(data_get($data, 'attended', []))
                ->merge(data_get($data, 'additional', []))
                ->map(fn (mixed $id): int => (int) $id)
                ->unique()
                ->values()
                ->all();

            $noShow = data_get($data, 'mark_no_show', false)
                ? collect($record->registrations->modelKeys())->diff($attended)->values()->all()
                : [];

            EventAttendanceService::recordAttendance($record, $attended, $noShow);

            $this->dispatchTo(ViewEvent::class, 'refreshRegistrations');
            $action->success();
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'takeAttendance';
    }
}
