<?php

declare(strict_types=1);

namespace App\Notifications\Channels;

use App\Services\SlackService;
use App\Support\Slack\SlackBlocks;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Notifications\Notification;

/**
 * Posts organization-wide notifications to the connected Slack channel, reusing the notification's
 * Discord embed so both services stay in step.
 */
class SlackPublicChannel
{
    /**
     * @throws ConnectionException
     */
    public function send(object $notifiable, Notification $notification): bool
    {
        if (! method_exists($notification, 'toDiscord')) {
            return false;
        }

        $message = $notification->toDiscord($notifiable);

        $payload = filled($message->embed)
            ? SlackBlocks::fromDiscordEmbed($message->embed, filled($message->body) ? $message->body : null)
            : ['text' => SlackBlocks::markdown((string) $message->body)];

        if (blank($payload['text']) && blank($payload['blocks'] ?? null)) {
            return false;
        }

        return SlackService::postMessage($payload);
    }
}
