<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Rank;
use App\Models\RankRequirement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RankRequirement>
 */
class RankRequirementFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rank_id' => Rank::factory(),
            'min_days_in_grade' => null,
            'min_days_in_service' => null,
            'min_events_attended_90d' => null,
            'required_qualification_ids' => null,
            'required_credential_ids' => null,
        ];
    }
}
