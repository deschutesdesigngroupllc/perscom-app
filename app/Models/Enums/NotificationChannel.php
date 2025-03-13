<?php

declare(strict_types=1);

namespace App\Models\Enums;

use App\Models\User;
use App\Notifications\Channels\DiscordPublicChannel;
use App\Settings\IntegrationSettings;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;
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

    public function getLabel(): ?string
    {
        return match ($this) {
            NotificationChannel::BROADCAST => 'Live',
            NotificationChannel::DATABASE => 'Dashboard',
            NotificationChannel::DISCORD_PRIVATE => 'Discord (Private)',
            NotificationChannel::DISCORD_PUBLIC => 'Discord (Public)',
            NotificationChannel::MAIL => 'Email',
            NotificationChannel::SMS => 'SMS',
        };
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            NotificationChannel::BROADCAST => 'Send the notification as an instant notification to the user\'s dashboard.',
            NotificationChannel::DATABASE => 'Store the notification in the user\'s database so they can review it later.',
            NotificationChannel::DISCORD_PRIVATE => 'Send the notification to the user\'s private Discord channel.',
            NotificationChannel::DISCORD_PUBLIC => 'Send the notification to your public Discord server. This will only send one notification regardless of the amount of recipients selected.',
            NotificationChannel::MAIL => 'Send the notification as an email.',
            NotificationChannel::SMS => 'Send the notification straight to the user\'s cell phone.',
        };
    }

    public function getChannel(): string
    {
        return match ($this) {
            NotificationChannel::DISCORD_PRIVATE => DiscordChannel::class,
            NotificationChannel::DISCORD_PUBLIC => DiscordPublicChannel::class,
            NotificationChannel::SMS => TwilioChannel::class,
            default => $this->value
        };
    }

    public function getEnabled(?User $notifiable = null): bool
    {
        /** @var IntegrationSettings $settings */
        $settings = app(IntegrationSettings::class);

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
            default => true
        };
    }
}
