<?php

declare(strict_types=1);

namespace App\Data\Roster;

use Spatie\LaravelData\Data;

class RosterMemberInsightData extends Data
{
    public function __construct(
        public ?int $daysInGrade = null,
        public bool $hasExpired = false,
        public bool $hasExpiring = false,
        public bool $promotionEligible = false,
        public ?string $nextRank = null,
    ) {}
}
