<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Jobs\Tenant;

use App\Jobs\Tenant\NotifyPromotionEligibleMembers;
use App\Models\PromotionNotification;
use App\Models\Rank;
use App\Models\RankRequirement;
use App\Models\User;
use App\Notifications\Tenant\PromotionEligible;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Tenant\TenantTestCase;

class NotifyPromotionEligibleMembersTest extends TenantTestCase
{
    public function test_eligible_members_are_notified_once_per_target_rank(): void
    {
        Notification::fake();

        $senior = Rank::factory()->createQuietly(['order' => 1]);
        $junior = Rank::factory()->createQuietly(['order' => 2]);
        RankRequirement::factory()->createQuietly(['rank_id' => $senior->getKey(), 'min_days_in_service' => 30]);

        $eligible = User::factory()->createQuietly(['rank_id' => $junior->getKey(), 'created_at' => now()->subDays(60)]);
        $notEligible = User::factory()->createQuietly(['rank_id' => $junior->getKey(), 'created_at' => now()->subDays(5)]);

        $this->runJob();
        $this->runJob();

        Notification::assertSentToTimes($eligible, PromotionEligible::class, 1);
        Notification::assertSentTo($eligible, PromotionEligible::class, fn (PromotionEligible $notification): bool => $notification->rank->is($senior));
        Notification::assertNotSentTo($notEligible, PromotionEligible::class);
        $this->assertDatabaseHas('promotion_notifications', ['user_id' => $eligible->getKey(), 'rank_id' => $senior->getKey()]);
        $this->assertSame(1, PromotionNotification::query()->count());
    }

    public function test_nothing_happens_without_rank_requirements(): void
    {
        Notification::fake();

        User::factory()->createQuietly();

        $this->runJob();

        Notification::assertNothingSent();
    }

    private function runJob(): void
    {
        $job = new NotifyPromotionEligibleMembers($this->tenant->getKey());
        $job->handle();
    }
}
