<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\EventResource\Actions;

use App\Filament\App\Resources\EventResource\Pages\ViewEvent;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Event;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Auth;

class RsvpAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('RSVP');
        $this->color('gray');

        $this->modalHeading('RSVP');
        $this->modalDescription('Please select your RSVP status for the event.');
        $this->modalSubmitActionLabel('Submit RSVP');

        $this->successNotificationTitle("You have successully RSVP'd for the event.");
        $this->visible(fn (Event $record): bool => $record->registration_enabled && ! $record->registration_deadline?->isPast());

        $this->fillForm(fn (Event $record): array => [
            'status' => $record->registrations->firstWhere('id', Auth::user()->getKey())?->registration->status?->value ?? EventRegistrationStatus::Going->value,
        ]);

        $this->schema([
            Select::make('status')
                ->default(EventRegistrationStatus::Going)
                ->helperText('Select your RSVP status for the event.')
                ->options(EventRegistrationStatus::class)
                ->required(),
        ]);

        $this->action(function (Event $record, Action $action, array $data): void {
            $status = data_get($data, 'status') ?? EventRegistrationStatus::Going;

            if ($record->registrations->contains(Auth::user())) {
                $record->registrations()->updateExistingPivot(Auth::user(), ['status' => $status]);
            } else {
                $record->registrations()->attach(Auth::user(), ['status' => $status]);
            }

            $this->dispatchTo(ViewEvent::class, 'refreshRegistrations');
            $action->success();
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'rsvp';
    }
}
