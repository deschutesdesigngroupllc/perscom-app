<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Twilio\TwilioChannel;

enum NotificationChannel: string implements HasDescription, HasLabel
{
    case BROADCAST = 'broadcast';
    case DATABASE = 'database';
    case DISCORD = 'discord';
    case MAIL = 'mail';
    case SMS = 'sms';

    public function getLabel(): ?string
    {
        return match ($this) {
            NotificationChannel::BROADCAST => 'Live',
            NotificationChannel::DATABASE => 'Dashboard',
            NotificationChannel::MAIL => 'Email',
            NotificationChannel::SMS => 'SMS',
            default => Str::of($this->value)->title()->toString()
        };
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            NotificationChannel::BROADCAST => 'Send this message as an instant notification to the user\'s dashboard.',
            NotificationChannel::DATABASE => 'Store this message in the user\'s database so they can review it later.',
            NotificationChannel::DISCORD => 'Send the message to your the user\'s private channel or a configured public channel.',
            NotificationChannel::MAIL => 'Send this message as an email.',
            NotificationChannel::SMS => 'Send this message straight to the user\'s cell phone.',
        };
    }

    public function getChannel(): string
    {
        return match ($this) {
            NotificationChannel::DISCORD => DiscordChannel::class,
            NotificationChannel::SMS => TwilioChannel::class,
            default => $this->value
        };
    }
}
