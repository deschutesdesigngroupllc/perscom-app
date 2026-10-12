<?php

declare(strict_types=1);

namespace App\Filament\App\Actions;

use App\Models\Calendar;
use App\Models\Event;
use App\Models\User;
use App\Services\CalendarFeedService;
use chillerlan\QRCode\QRCode;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\View;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

/**
 * Lets the signed-in member subscribe to the organization's events from Google, Apple or Outlook
 * calendars using their personal feed link.
 */
class SubscribeToCalendarAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Subscribe');
        $this->icon(Heroicon::OutlinedRss);
        $this->color('gray');

        $this->modalHeading('Subscribe to Calendar');
        $this->modalDescription('Add events to the calendar app you already use. Subscribed calendars update automatically as events change.');
        $this->modalIcon(Heroicon::OutlinedCalendarDays);
        $this->modalWidth('lg');
        $this->modalSubmitAction(false);
        $this->modalCancelActionLabel('Close');

        $this->visible(fn (): bool => Auth::user()?->can('viewAny', Event::class) ?? false);

        $this->schema([
            Select::make('calendar')
                ->label('Calendar')
                ->helperText('Choose which events to add to your calendar app.')
                ->options(fn (): array => ['all' => 'All my events'] + Calendar::query()->orderBy('name')->pluck('name', 'id')->all())
                ->default('all')
                ->selectablePlaceholder(false)
                ->native(false)
                ->live(),
            View::make('filament.app.calendars.subscribe')
                ->viewData(fn (Get $get): array => static::links($get('calendar'))),
        ]);

        $this->extraModalFooterActions([
            Action::make('resetCalendarLink')
                ->label('Reset Link')
                ->icon(Heroicon::OutlinedArrowPath)
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Reset Calendar Link')
                ->modalDescription('Your current subscription links will stop working. Calendar apps subscribed with the old link must be subscribed again.')
                ->modalSubmitActionLabel('Reset Link')
                ->action(function (): void {
                    /** @var User $user */
                    $user = Auth::user();

                    CalendarFeedService::resetToken($user);

                    Notification::make()
                        ->success()
                        ->title('Your calendar link has been reset.')
                        ->send();
                }),
        ]);
    }

    public static function getDefaultName(): ?string
    {
        return 'subscribeToCalendar';
    }

    /**
     * @return array{url: string, webcalUrl: string, googleUrl: string, outlookUrl: string, qrCode: string, name: string}
     */
    public static function links(mixed $calendarId): array
    {
        /** @var User $user */
        $user = Auth::user();

        $calendar = filled($calendarId) && $calendarId !== 'all' ? Calendar::query()->find($calendarId) : null;

        [$url, $name] = $calendar instanceof Calendar
            ? [CalendarFeedService::calendarFeedUrl($user, $calendar), CalendarFeedService::calendarFeedName($calendar)]
            : [CalendarFeedService::personalFeedUrl($user), CalendarFeedService::personalFeedName()];

        $webcalUrl = CalendarFeedService::webcalUrl($url);

        return [
            'url' => $url,
            'webcalUrl' => $webcalUrl,
            'googleUrl' => CalendarFeedService::googleSubscribeUrl($url),
            'outlookUrl' => CalendarFeedService::outlookSubscribeUrl($url, $name),
            'qrCode' => (new QRCode)->render($webcalUrl),
            'name' => $name,
        ];
    }
}
