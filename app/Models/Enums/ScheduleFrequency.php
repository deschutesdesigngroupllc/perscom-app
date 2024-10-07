<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasLabel;

enum ScheduleFrequency: string implements HasLabel
{
    case DAILY = 'DAILY';
    case WEEKLY = 'WEEKLY';
    case MONTHLY = 'MONTHLY';
    case YEARLY = 'YEARLY';

    public function getLabel(): ?string
    {
        return match ($this) {
            ScheduleFrequency::DAILY => 'Day(s)',
            ScheduleFrequency::WEEKLY => 'Week(s)',
            ScheduleFrequency::MONTHLY => 'Month(s)',
            ScheduleFrequency::YEARLY => 'Year(s)'
        };
    }
}
