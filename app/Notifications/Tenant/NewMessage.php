<?php

declare(strict_types=1);

namespace App\Notifications\Tenant;

use App\Mail\Tenant\NewMessage as NewMessageMail;
use App\Models\Enums\NotificationChannel;
use App\Models\Message;
use App\Models\User;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use League\HTMLToMarkdown\HtmlConverter;
use NotificationChannels\Discord\DiscordMessage;
use NotificationChannels\Twilio\TwilioMessage;
use NotificationChannels\Twilio\TwilioSmsMessage;

class NewMessage extends Notification implements ShouldQueue
{
    use Queueable;

    protected FilamentNotification $notification;

    public function __construct(protected Message $message)
    {
        $this->notification = FilamentNotification::make()
            ->title('New Message')
            ->body($this->message->message)
            ->info();
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        /** @var Collection<int, NotificationChannel> $channels */
        $channels = $this->message->channels;

        return $channels->map(fn (NotificationChannel $channel) => $channel->getChannel())->toArray();
    }

    public function toMail(User $notifiable): NewMessageMail
    {
        return (new NewMessageMail($this->message))->to($notifiable->email);
    }

    public function toBroadcast(User $notifiable): BroadcastMessage
    {
        return $this->notification->getBroadcastMessage();
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(): array
    {
        return $this->notification->getDatabaseMessage();
    }

    public function toDiscord(User $notifiable): DiscordMessage
    {
        $converter = new HtmlConverter([
            'strip_tags' => true,
            'remove_nodes' => true,
        ]);

        return DiscordMessage::create($converter->convert($this->message->message));
    }

    public function toTwilio(User $notifiable): TwilioSmsMessage|TwilioMessage
    {
        return (new TwilioSmsMessage)
            ->from(config('services.twilio.from'))
            ->content($this->message->message);
    }
}
