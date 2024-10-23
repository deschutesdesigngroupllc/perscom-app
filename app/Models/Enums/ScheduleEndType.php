<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum ScheduleEndType: string implements HasLabel
{
    case NEVER = 'never';
    case ON = 'on';
    case AFTER = 'after';

    public function getLabel(): ?string
    {
        return Str::of($this->value)
            ->title()
            ->toString();
    }
}
