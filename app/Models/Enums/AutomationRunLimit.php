<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum AutomationRunLimit: string implements HasDescription, HasLabel
{
    case ALWAYS = 'always';
    case ONCE_PER_MODEL = 'once_per_model';
    case ONCE_PER_DAY = 'once_per_day';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ALWAYS => 'Every Time',
            self::ONCE_PER_MODEL => 'Once Per Resource',
            self::ONCE_PER_DAY => 'Once Per Resource Per Day',
        };
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            self::ALWAYS => 'Run every time the trigger fires and the condition passes.',
            self::ONCE_PER_MODEL => 'Run only the first time the condition passes for a given resource.',
            self::ONCE_PER_DAY => 'Run at most once per day for a given resource.',
        };
    }
}
