<?php

declare(strict_types=1);

namespace App\Models\Enums;

use App\Models\User;
use App\Notifications\Channels\DiscordPublicChannel;
use App\Notifications\Channels\SlackPublicChannel;
use App\Settings\IntegrationSettings;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Collection;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Twilio\TwilioChannel;

enum NotificationChannel: string implements HasDescription, HasLabel
{
    case BROADCAST = 'broadcast';
    case DATABASE = 'database';
    case DISCORD_PRIVATE = 'discord_private';
    case DISCORD_PUBLIC = 'discord_public';
    case MAIL = 'mail';
    case SMS = 'sms';
    case SLACK = 'slack';

    /**
     * @param  iterable<int, NotificationChannel|string>|NotificationChannel|string|null  $channels
     * @return Collection<int, NotificationChannel>
     */
    public static function organizationWide(mixed $channels): Collection
    {
        return Collection::wrap($channels)
            ->map(fn (NotificationChannel|string $channel): ?NotificationChannel => $channel instanceof NotificationChannel ? $channel : NotificationChannel::tryFrom($channel))
            ->filter(fn (?NotificationChannel $channel): bool => $channel?->isOrganizationWide() ?? false)
            ->unique()
            ->values();
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            NotificationChannel::BROADCAST => 'Live',
            NotificationChannel::DATABASE => 'Dashboard',
            NotificationChannel::DISCORD_PRIVATE => 'Discord (Private)',
            NotificationChannel::DISCORD_PUBLIC => 'Discord (Public)',
            NotificationChannel::MAIL => 'Email',
            NotificationChannel::SMS => 'SMS',
            NotificationChannel::SLACK => 'Slack',
        };
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            NotificationChannel::BROADCAST => "Send the notification as an instant notification to the user's dashboard.",
            NotificationChannel::DATABASE => "Store the notification in the user's database so they can review it later.",
            NotificationChannel::DISCORD_PRIVATE => "Send the notification to the user's private Discord channel.",
            NotificationChannel::DISCORD_PUBLIC => 'Send the notification to your public Discord server. This will only send one notification regardless of the amount of recipients selected.',
            NotificationChannel::MAIL => 'Send the notification as an email.',
            NotificationChannel::SMS => "Send the notification straight to the user's cell phone.",
            NotificationChannel::SLACK => 'Send the notification to your connected Slack channel. This will only send one notification regardless of the amount of recipients selected.',
        };
    }

    /**
     * Organization-wide channels post one message to a shared Discord or Slack channel rather than
     * one message per recipient.
     */
    public function isOrganizationWide(): bool
    {
        return in_array($this, [NotificationChannel::DISCORD_PUBLIC, NotificationChannel::SLACK], true);
    }

    public function getChannel(): string
    {
        return match ($this) {
            NotificationChannel::DISCORD_PRIVATE => DiscordChannel::class,
            NotificationChannel::DISCORD_PUBLIC => DiscordPublicChannel::class,
            NotificationChannel::SMS => TwilioChannel::class,
            NotificationChannel::SLACK => SlackPublicChannel::class,
            default => $this->value
        };
    }

    public function getEnabled(?User $notifiable = null): bool
    {
        /** @var IntegrationSettings $settings */
        $settings = resolve(IntegrationSettings::class);

        if ($this === NotificationChannel::SMS && $notifiable && blank($notifiable->phone_number)) {
            return false;
        }

        if ($this === NotificationChannel::DISCORD_PRIVATE && $notifiable && blank($notifiable->discord_private_channel_id)) {
            return false;
        }

        return match ($this) {
            NotificationChannel::DISCORD_PRIVATE => filled(data_get($settings->discord_settings, 'discord_enabled')),
            NotificationChannel::DISCORD_PUBLIC => filled(data_get($settings->discord_settings, 'discord_enabled'))
                && filled(data_get($settings->discord_settings, 'discord_server'))
                && filled(data_get($settings->discord_settings, 'discord_channel')),
            NotificationChannel::SMS => filled(data_get($settings->sms_settings, 'sms_enabled')),
            NotificationChannel::SLACK => filled(data_get($settings->slack_settings, 'slack_enabled'))
                && filled(data_get($settings->slack_settings, 'bot_token'))
                && filled(data_get($settings->slack_settings, 'slack_channel')),
            default => true
        };
    }
}
