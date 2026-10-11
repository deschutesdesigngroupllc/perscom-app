<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Events;

use App\Exceptions\EventCheckInException;
use App\Filament\App\Resources\EventResource;
use App\Models\Event;
use App\Models\User;
use App\Services\EventAttendanceService;
use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CheckInController
{
    public function __invoke(Event $event): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            EventAttendanceService::checkIn($event, $user);

            Notification::make()
                ->success()
                ->title(__('You have been checked in.'))
                ->body(__('Your attendance for :event has been recorded.', ['event' => $event->name]))
                ->send();
        } catch (EventCheckInException $exception) {
            Notification::make()
                ->danger()
                ->title(__('Unable to check in.'))
                ->body($exception->getMessage())
                ->send();
        }

        return redirect()->to(EventResource::getUrl('view', ['record' => $event]));
    }
}
