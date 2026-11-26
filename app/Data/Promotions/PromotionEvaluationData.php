<?php

declare(strict_types=1);

namespace App\Data\Promotions;

use App\Models\Rank;
use App\Models\User;
use Spatie\LaravelData\Data;

class PromotionEvaluationData extends Data
{
    /**
     * @param  array<int, RequirementResultData>  $requirements
     */
    public function __construct(
        public User $user,
        public ?Rank $currentRank,
        public ?Rank $nextRank,
        public bool $eligible,
        public array $requirements = [],
        public ?string $reason = null,
    ) {}

    /**
     * Get the requirements the member meets.
     *
     * @return array<int, RequirementResultData>
     */
    public function metRequirements(): array
    {
        return collect($this->requirements)
            ->filter(fn (RequirementResultData $result): bool => $result->met)
            ->values()
            ->all();
    }

    /**
     * Get the requirements the member does not yet meet.
     *
     * @return array<int, RequirementResultData>
     */
    public function unmetRequirements(): array
    {
        return collect($this->requirements)
            ->reject(fn (RequirementResultData $result): bool => $result->met)
            ->values()
            ->all();
    }
}
