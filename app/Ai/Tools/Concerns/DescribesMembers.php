<?php

declare(strict_types=1);

namespace App\Ai\Tools\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\Date;

trait DescribesMembers
{
    /**
     * @return array<string, mixed>
     */
    protected function describeMember(User $user): array
    {
        $lastRankChange = $user->getAttribute('last_rank_record_at');

        return [
            'id' => $user->getKey(),
            'name' => $user->name,
            'rank' => $user->rank?->name,
            'rank_id' => $user->rank_id,
            'unit' => $user->unit?->name,
            'unit_id' => $user->unit_id,
            'position' => $user->position?->name,
            'position_id' => $user->position_id,
            'specialty' => $user->specialty?->name,
            'status' => $user->status?->name,
            'status_id' => $user->status_id,
            'days_in_grade' => filled($lastRankChange) ? (int) Date::parse($lastRankChange)->diffInDays(now()) : null,
            'last_seen_at' => $user->last_seen_at?->toDateTimeString(),
        ];
    }
}
