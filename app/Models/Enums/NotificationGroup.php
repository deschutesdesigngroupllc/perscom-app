<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum NotificationGroup: string implements HasLabel
{
    case EVENTS = 'events';
    case RECORDS = 'records';

    public function getLabel(): ?string
    {
        return Str::of($this->value)
            ->title()
            ->toString();
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            NotificationGroup::EVENTS => 'All notifications regarding calendar and events.',
            NotificationGroup::RECORDS => 'All notifications regarding account records.',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            NotificationGroup::EVENTS => 'heroicon-o-calendar-days',
            NotificationGroup::RECORDS => 'heroicon-o-rectangle-stack',
        };
    }
}
