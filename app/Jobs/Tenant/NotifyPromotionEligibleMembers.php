<?php

declare(strict_types=1);

namespace App\Jobs\Tenant;

use App\Contracts\RunsPerTenant;
use App\Data\Promotions\PromotionEvaluationData;
use App\Jobs\Concerns\RunsForTenant;
use App\Models\PromotionNotification;
use App\Models\RankRequirement;
use App\Notifications\Tenant\PromotionEligible;
use App\Services\PromotionEligibilityService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyPromotionEligibleMembers implements RunsPerTenant, ShouldQueue
{
    use Batchable;
    use InteractsWithQueue;
    use Queueable;
    use RunsForTenant;

    public function __construct(public ?int $tenantKey = null)
    {
        $this->configureForTenancy();
    }

    public function work(): void
    {
        if (! RankRequirement::query()->exists()) {
            return;
        }

        PromotionEligibilityService::eligible()
            ->reject(fn (PromotionEvaluationData $evaluation): bool => PromotionNotification::query()
                ->where('user_id', $evaluation->user->getKey())
                ->where('rank_id', $evaluation->nextRank->getKey())
                ->exists())
            ->each(function (PromotionEvaluationData $evaluation): void {
                PromotionNotification::create([
                    'user_id' => $evaluation->user->getKey(),
                    'rank_id' => $evaluation->nextRank->getKey(),
                ]);

                Notification::send($evaluation->user, new PromotionEligible($evaluation->nextRank));
            });
    }
}
