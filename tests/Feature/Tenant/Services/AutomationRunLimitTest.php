<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Services;

use App\Events\Automations\ScheduledTriggerFired;
use App\Events\Automations\UserCreated;
use App\Models\Automation;
use App\Models\AutomationLog;
use App\Models\Enums\AutomationLogStatus;
use App\Models\Enums\AutomationRunLimit;
use App\Models\Enums\AutomationTrigger;
use App\Models\Message;
use App\Models\User;
use App\Services\AutomationService;
use Mockery;
use Tests\Feature\Tenant\TenantTestCase;

class AutomationRunLimitTest extends TenantTestCase
{
    public function test_always_runs_every_time(): void
    {
        $automation = $this->messageAutomation(AutomationRunLimit::ALWAYS);
        $user = User::factory()->createQuietly();

        $this->process($user);
        $this->process($user);

        $this->assertSame(2, Message::query()->count());
        $this->assertSame(0, $automation->runs()->count());
    }

    public function test_once_per_model_prevents_repeat_runs_for_the_same_subject(): void
    {
        $automation = $this->messageAutomation(AutomationRunLimit::ONCE_PER_MODEL);
        $user = User::factory()->createQuietly();
        $other = User::factory()->createQuietly();

        $first = $this->process($user);
        $second = $this->process($user);
        $this->process($other);

        $this->assertSame(AutomationLogStatus::EXECUTED, $first->first()->status);
        $this->assertSame(AutomationLogStatus::SKIPPED, $second->first()->status);
        $this->assertSame(2, Message::query()->count());
        $this->assertSame(2, $automation->runs()->count());
    }

    public function test_once_per_day_allows_a_run_the_next_day(): void
    {
        $this->messageAutomation(AutomationRunLimit::ONCE_PER_DAY);
        $user = User::factory()->createQuietly();

        $this->process($user);
        $this->process($user);

        $this->assertSame(1, Message::query()->count());

        $this->travel(1)->day();

        $this->process($user);

        $this->assertSame(2, Message::query()->count());
    }

    public function test_run_limit_is_checked_through_the_automation(): void
    {
        $automation = Mockery::spy($this->messageAutomation(AutomationRunLimit::ONCE_PER_MODEL));
        $user = User::factory()->createQuietly();

        resolve(AutomationService::class)->executeAutomation($automation, $this->event($user));

        $automation->shouldHaveReceived('hasReachedRunLimitFor')->once();
        $automation->shouldHaveReceived('recordRunFor')->once();
    }

    public function test_scheduled_condition_failures_are_not_logged(): void
    {
        Automation::factory()
            ->messageAction()
            ->forTrigger(AutomationTrigger::USER_DAILY_REVIEW)
            ->createQuietly(['condition' => 'model["time_in_grade_days"] >= 30']);

        $user = User::factory()->createQuietly();

        $results = resolve(AutomationService::class)->process(
            new ScheduledTriggerFired($user, AutomationTrigger::USER_DAILY_REVIEW, ['time_in_grade_days' => 3])
        );

        $this->assertSame(AutomationLogStatus::CONDITION_FAILED, $results->first()->status);
        $this->assertSame(0, AutomationLog::query()->count());
    }

    public function test_scheduled_trigger_executes_when_condition_passes(): void
    {
        Automation::factory()
            ->messageAction()
            ->forTrigger(AutomationTrigger::USER_DAILY_REVIEW)
            ->createQuietly(['condition' => 'model["time_in_grade_days"] >= 30']);

        $user = User::factory()->createQuietly();

        $results = resolve(AutomationService::class)->process(
            new ScheduledTriggerFired($user, AutomationTrigger::USER_DAILY_REVIEW, ['time_in_grade_days' => 31])
        );

        $this->assertSame(AutomationLogStatus::EXECUTED, $results->first()->status);
        $this->assertSame(1, Message::query()->count());
    }

    private function messageAutomation(AutomationRunLimit $runLimit): Automation
    {
        return Automation::factory()
            ->messageAction()
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->createQuietly(['run_limit' => $runLimit]);
    }

    private function event(User $user): UserCreated
    {
        return new UserCreated(subject: $user, changedAttributes: []);
    }

    private function process(User $user): mixed
    {
        return resolve(AutomationService::class)->process($this->event($user));
    }
}
