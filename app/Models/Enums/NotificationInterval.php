<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasLabel;

enum NotificationInterval: string implements HasLabel
{
    case PT0M = 'pt0m';
    case PT15M = 'pt15m';
    case PT1H = 'pt1h';
    case P1D = 'p1d';
    case P1W = 'p1w';

    public function getLabel(): ?string
    {
        return match ($this) {
            NotificationInterval::PT0M => 'At time of event',
            NotificationInterval::PT15M => '15 minutes before',
            NotificationInterval::PT1H => '1 hour before',
            NotificationInterval::P1D => '1 day before',
            NotificationInterval::P1W => '1 week before',
        };
    }
}
