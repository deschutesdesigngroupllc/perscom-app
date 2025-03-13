<?php

declare(strict_types=1);

namespace App\Notifications\Channels;

use App\Settings\IntegrationSettings;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\Discord;

class DiscordPublicChannel
{
    public function __construct(protected Discord $discord) {}

    /**
     * @return array<string, mixed>|null
     */
    public function send(object $notifiable, Notification $notification): ?array
    {
        /** @var IntegrationSettings $settings */
        $settings = app(IntegrationSettings::class);

        $channel = data_get($settings->discord_settings, 'discord_channel') ?? null;

        if (! $channel || ! method_exists($notification, 'toDiscord')) {
            return null;
        }

        $message = $notification->toDiscord($notifiable);

        $data = [
            'content' => $message->body,
        ];

        if (count($message->embed) > 0) {
            $data['embeds'] = [$message->embed];
        }

        if (count($message->components) > 0) {
            $data['components'] = $message->components;
        }

        return $this->discord->send($channel, $data);
    }
}
