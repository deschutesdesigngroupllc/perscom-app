<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\EventResource\Pages;

use App\Filament\App\Resources\EventResource;
use App\Models\Event;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewEvent extends ViewRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('url')
                ->url(fn (?Event $record) => $record->url)
                ->openUrlInNewTab()
                ->color('gray')
                ->label('Open')
                ->hidden(fn (?Event $record) => is_null($record->url)),
            Actions\Action::make('register')
                ->color('success')
                ->label('Register')
                ->visible(fn (?Event $record) => $record->registration_enabled && ! $record->registration_deadline?->isPast() && ! $record->registrations->contains(Auth::user()))
                ->successNotificationTitle('You have successfully registered for the event.')
                ->action(function (?Event $record, Actions\Action $action) {
                    $record->registrations()->attach(Auth::user());
                    $this->dispatch('refreshRegistrations');
                    $action->success();
                }),
            Actions\Action::make('unregister')
                ->color('danger')
                ->label('Unregister')
                ->visible(fn (?Event $record) => $record->registrations->contains(Auth::user()))
                ->successNotificationTitle('You have successfully unregistered for the event.')
                ->action(function (?Event $record, Actions\Action $action) {
                    $record->registrations()->detach(Auth::user());
                    $this->dispatch('refreshRegistrations');
                    $action->success();
                }),
            Actions\EditAction::make(),
        ];
    }
}
