<?php

declare(strict_types=1);

namespace App\Notifications\Channels;

use App\Features\AdvancedNotificationsFeature;
use App\Settings\FeatureSettings;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\Discord;

class DiscordPublicChannel
{
    protected Discord $discord;

    public function __construct(Discord $discord)
    {
        $this->discord = $discord;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function send(object $notifiable, Notification $notification): ?array
    {
        /** @var FeatureSettings $settings */
        $settings = app(FeatureSettings::class);

        $key = AdvancedNotificationsFeature::settingsKey();

        $channel = data_get($settings->$key, 'discord_channel') ?? null;

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
