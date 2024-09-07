<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum RankRecordType: int implements HasColor, HasLabel
{
    case PROMOTION = 0;
    case DEMOTION = 1;
    case LATERAL = 2;

    public function getLabel(): string
    {
        return Str::title($this->name);
    }

    public function getColor(): string
    {
        return match ($this) {
            RankRecordType::PROMOTION => 'success',
            RankRecordType::DEMOTION => 'danger',
            RankRecordType::LATERAL => 'info'
        };
    }
}
