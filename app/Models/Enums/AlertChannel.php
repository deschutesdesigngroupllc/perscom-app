<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum AlertChannel: string implements HasDescription, HasLabel
{
    case DASHBOARD = 'dashboard';
    case MAIL = 'mail';
    case SLACK = 'slack';

    public function getLabel(): ?string
    {
        return Str::of($this->value)
            ->title()
            ->toString();
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            AlertChannel::DASHBOARD => 'Post this alert to each tenant\'s dashboard.',
            AlertChannel::MAIL => 'Send this alert to each tenant.',
            AlertChannel::SLACK => 'Post this alert to the PERSCOM slack workspace.',
        };
    }
}
