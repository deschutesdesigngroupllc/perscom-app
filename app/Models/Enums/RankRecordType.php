<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum RankRecordType: int implements HasColor, HasIcon, HasLabel
{
    case PROMOTION = 0;
    case DEMOTION = 1;
    case LATERAL = 2;
    case TRANSFER = 3;

    public function getLabel(): string
    {
        return Str::title($this->name);
    }

    public function getColor(): string
    {
        return match ($this) {
            RankRecordType::PROMOTION => 'success',
            RankRecordType::DEMOTION => 'danger',
            RankRecordType::LATERAL, RankRecordType::TRANSFER => 'info'
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            RankRecordType::PROMOTION => 'heroicon-o-arrow-up-circle',
            RankRecordType::DEMOTION => 'heroicon-o-arrow-up-circle',
            RankRecordType::LATERAL, RankRecordType::TRANSFER => 'heroicon-o-arrow-right-circle',
        };
    }
}
