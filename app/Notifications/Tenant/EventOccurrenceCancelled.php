<?php

declare(strict_types=1);

namespace App\Notifications\Tenant;

use App\Contracts\NotificationCanBeManaged;
use App\Filament\App\Resources\EventResource;
use App\Mail\Tenant\EventOccurrenceCancelledMail;
use App\Models\Enums\NotificationChannel;
use App\Models\Enums\NotificationGroup;
use App\Models\Event;
use App\Models\EventOccurrence;
use App\Models\User;
use App\Services\TwilioService;
use App\Services\UserSettingsService;
use App\Support\Discord\DiscordEmbed;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use NotificationChannels\Discord\DiscordMessage;
use NotificationChannels\Twilio\TwilioMessage;
use NotificationChannels\Twilio\TwilioSmsMessage;

class EventOccurrenceCancelled extends Notification implements NotificationCanBeManaged, ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public bool $deleteWhenMissingModels = true;

    public function __construct(public EventOccurrence $occurrence)
    {
        //
    }

    public static function notificationGroup(): NotificationGroup
    {
        return NotificationGroup::EVENTS;
    }

    public static function notificationTitle(): string
    {
        return 'Cancelled Event Dates';
    }

    public static function notificationDescription(): string
    {
        return 'Sent when a date of an event you plan to attend is cancelled.';
    }

    /**
     * The event's configured channels, or the in-app and email channels when the event has none,
     * limited to the channels the member can receive.
     *
     * @return string[]
     */
    public function via(User $notifiable): array
    {
        /** @var Collection<int, NotificationChannel> $configured */
        $configured = collect($this->event()->notifications_channels ?? []);

        $channels = $configured->isNotEmpty()
            ? $configured
            : collect([NotificationChannel::DATABASE, NotificationChannel::BROADCAST, NotificationChannel::MAIL]);

        return $channels
            ->reject(fn (NotificationChannel $channel): bool => $channel->isOrganizationWide())
            ->filter(fn (NotificationChannel $channel): bool => $channel->getEnabled($notifiable))
            ->map(fn (NotificationChannel $channel): string => $channel->getChannel())
            ->unique()
            ->values()
            ->all();
    }

    public function toMail(User $notifiable): EventOccurrenceCancelledMail
    {
        return new EventOccurrenceCancelledMail($this->occurrence, $this->date($notifiable), $this->url())->to($notifiable->email);
    }

    public function toBroadcast(User $notifiable): BroadcastMessage
    {
        return $this->filamentNotification($notifiable)->getBroadcastMessage();
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(User $notifiable): array
    {
        return $this->filamentNotification($notifiable)->getDatabaseMessage();
    }

    public function toDiscord(User $notifiable): DiscordMessage
    {
        $embed = DiscordEmbed::make()
            ->title('Event Cancelled - '.$this->event()->name)
            ->description(strip_tags($this->message($notifiable)))
            ->url($this->url())
            ->color('#ef4444')
            ->field('Date', sprintf('<t:%d:F>', $this->occurrence->starts->getTimestamp()))
            ->field('Reason', $this->occurrence->notes)
            ->field('Calendar', $this->event()->calendar?->name)
            ->timestamp($this->occurrence->starts);

        return DiscordMessage::create(body: '', embed: $embed->toArray());
    }

    public function toTwilio(User $notifiable): TwilioSmsMessage|TwilioMessage|null
    {
        /** @var TwilioService $service */
        $service = resolve(TwilioService::class);

        if (! $channel = $service->toNotificationChannel(
            message: TwilioService::formatText($this->message($notifiable))
        )) {
            return null;
        }

        return $channel;
    }

    protected function event(): Event
    {
        /** @var Event $event */
        $event = $this->occurrence->event;

        return $event;
    }

    protected function filamentNotification(User $notifiable): FilamentNotification
    {
        return FilamentNotification::make()
            ->title('Event Cancelled - '.$this->event()->name)
            ->body($this->message($notifiable))
            ->actions([
                Action::make('Open event')
                    ->button()
                    ->url($this->url()),
            ])
            ->danger();
    }

    protected function message(User $notifiable): string
    {
        $message = sprintf('%s on %s has been cancelled.', $this->event()->name, $this->date($notifiable));

        if (filled($this->occurrence->notes)) {
            $message .= ' Reason: '.$this->occurrence->notes;
        }

        return $message;
    }

    protected function date(User $notifiable): string
    {
        $starts = $this->occurrence->starts->copy()->setTimezone(UserSettingsService::timezone($notifiable));

        return $this->event()->all_day
            ? $starts->toFormattedDayDateString()
            : sprintf('%s at %s', $starts->toFormattedDayDateString(), $starts->format('g:i A'));
    }

    protected function url(): string
    {
        return EventResource::getUrl('view', [
            'record' => $this->event(),
            'occurrence' => $this->occurrence->starts->getTimestamp(),
        ], panel: 'app');
    }
}
