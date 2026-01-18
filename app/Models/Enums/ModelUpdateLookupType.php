<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasLabel;

enum ModelUpdateLookupType: string implements HasLabel
{
    case EXPRESSION = 'expression';
    case QUERY = 'query';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::EXPRESSION => 'By Expression (ID)',
            self::QUERY => 'By Query Conditions',
        };
    }
}
