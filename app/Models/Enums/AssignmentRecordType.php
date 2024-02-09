<?php

namespace App\Models\Enums;

use Illuminate\Support\Str;

enum AssignmentRecordType: string
{
    case PRIMARY = 'primary';
    case SECONDARY = 'secondary';

    public function getLabel(): string
    {
        return Str::title($this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PRIMARY => 'success',
            self::SECONDARY => 'info'
        };
    }
}
