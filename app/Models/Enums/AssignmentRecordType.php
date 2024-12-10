<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum AssignmentRecordType: string implements HasColor, HasLabel
{
    case PRIMARY = 'primary';
    case SECONDARY = 'secondary';
    case SLOT = 'slot';

    public function getLabel(): string
    {
        return Str::title($this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PRIMARY => 'success',
            self::SECONDARY => 'info',
            self::SLOT => 'gray'
        };
    }
}
