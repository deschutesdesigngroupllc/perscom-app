<?php

namespace App\Models\Enums;

use Illuminate\Support\Str;

enum RankRecordType: int
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
            RankRecordType::PROMOTION => '#16A34A',
            RankRecordType::DEMOTION => '#DC2626',
            RankRecordType::LATERAL => '#334155'
        };
    }
}
