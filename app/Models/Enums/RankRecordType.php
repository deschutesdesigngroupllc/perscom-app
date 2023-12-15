<?php

namespace App\Models\Enums;

enum RankRecordType: int
{
    case RANK_RECORD_PROMOTION = 0;
    case RANK_RECORD_DEMOTION = 1;
    case RANK_RECORD_LATERAL = 2;

    public function getLabel(): ?string
    {
        return match ($this) {
            RankRecordType::RANK_RECORD_PROMOTION => 'Promotion',
            RankRecordType::RANK_RECORD_DEMOTION => 'Demotion',
            RankRecordType::RANK_RECORD_LATERAL => 'Lateral'
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            RankRecordType::RANK_RECORD_PROMOTION => '#16A34A',
            RankRecordType::RANK_RECORD_DEMOTION => '#DC2626',
            RankRecordType::RANK_RECORD_LATERAL => '#334155'
        };
    }
}
