<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Jobs\Tenant;

use App\Events\Automations\ScheduledTriggerFired;
use App\Jobs\Tenant\RunDailyUserReview;
use App\Models\Automation;
use App\Models\Enums\AutomationTrigger;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Event as EventModel;
use App\Models\QualificationRecord;
use App\Models\RankRecord;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Tests\Feature\Tenant\TenantTestCase;

class RunDailyUserReviewTest extends TenantTestCase
{
    public function test_nothing_is_dispatched_without_daily_review_automations(): void
    {
        Event::fake([ScheduledTriggerFired::class]);

        User::factory()->createQuietly();

        $this->runJob();

        Event::assertNotDispatched(ScheduledTriggerFired::class);
    }

    public function test_daily_review_is_dispatched_for_each_user(): void
    {
        Event::fake([ScheduledTriggerFired::class]);

        Automation::factory()->messageAction()->forTrigger(AutomationTrigger::USER_DAILY_REVIEW)->createQuietly();
        $users = User::factory()->count(3)->createQuietly();

        $this->runJob();

        Event::assertDispatchedTimes(ScheduledTriggerFired::class, User::query()->count());
        $users->each(fn (User $user) => Event::assertDispatched(
            ScheduledTriggerFired::class,
            fn (ScheduledTriggerFired $event): bool => $event->getSubject()->is($user)
                && $event->getTriggerType() === AutomationTrigger::USER_DAILY_REVIEW->value,
        ));
    }

    public function test_context_contains_review_metrics(): void
    {
        $this->freezeSecond();

        $user = User::factory()->createQuietly([
            'created_at' => now()->subDays(100),
            'last_seen_at' => now()->subDays(4),
        ]);

        RankRecord::factory()->for($user)->createQuietly(['created_at' => now()->subDays(40)]);

        $active = QualificationRecord::factory()->for($user)->createQuietly(['expires_at' => now()->addDays(5)]);
        QualificationRecord::factory()->for($user)->createQuietly(['expires_at' => now()->subDay()]);

        $attended = EventModel::factory()->createQuietly();
        $missed = EventModel::factory()->createQuietly();
        $user->events()->attach($attended, ['status' => EventRegistrationStatus::ATTENDED->value]);
        $user->events()->attach($missed, ['status' => EventRegistrationStatus::NO_SHOW->value]);

        $context = RunDailyUserReview::contextFor($user->refresh());

        $this->assertSame(40, $context['time_in_grade_days']);
        $this->assertSame(100, $context['time_in_service_days']);
        $this->assertSame(4, $context['days_since_last_seen']);
        $this->assertSame(1, $context['events_attended_30d']);
        $this->assertSame(1, $context['events_missed_30d']);
        $this->assertSame([$active->qualification_id], $context['qualification_ids']);
    }

    public function test_context_is_merged_into_the_model_for_expressions(): void
    {
        $user = User::factory()->createQuietly();

        $event = new ScheduledTriggerFired($user, AutomationTrigger::USER_DAILY_REVIEW, ['time_in_grade_days' => 12]);

        $context = $event->getExpressionContext()->toExpressionArray();

        $this->assertSame(12, $context['model']['time_in_grade_days']);
        $this->assertSame($user->getKey(), $context['model']['id']);
        $this->assertArrayNotHasKey('time_in_grade_days', $user->toArray());
    }

    private function runJob(): void
    {
        $job = new RunDailyUserReview($this->tenant->getKey());
        $job->handle();
    }
}
