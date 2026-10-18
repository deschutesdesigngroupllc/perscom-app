<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Jobs\Tenant;

use App\Events\Automations\ScheduledTriggerFired;
use App\Jobs\Tenant\ProcessExpiringRecords;
use App\Models\Credential;
use App\Models\Enums\AutomationTrigger;
use App\Models\Qualification;
use App\Models\QualificationRecord;
use App\Models\TrainingRecord;
use App\Models\TrainingRecordCredential;
use App\Models\User;
use App\Notifications\Tenant\CredentialExpiring;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Tenant\TenantTestCase;

class ProcessExpiringRecordsTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Event::fake([ScheduledTriggerFired::class]);
        Notification::fake();
        $this->travelTo(now()->setTime(12, 0));
    }

    public function test_credential_expiring_trigger_fires_with_context(): void
    {
        [$user, $pivot] = $this->credentialExpiringInDays(10, 'First Aid');

        $this->runJob();

        Event::assertDispatched(ScheduledTriggerFired::class, function (ScheduledTriggerFired $event) use ($user, $pivot): bool {
            $context = $event->getExpressionContext()->toExpressionArray();

            return $event->getTriggerType() === AutomationTrigger::CREDENTIAL_EXPIRING->value
                && $event->getSubject()->is($pivot)
                && $context['model']['user_id'] === $user->getKey()
                && $context['model']['credential_name'] === 'First Aid'
                && $context['model']['days_until_expiry'] === 10;
        });
    }

    public function test_credential_expired_trigger_fires_on_the_day_it_expires(): void
    {
        $this->credentialExpiringInDays(0);

        $this->runJob();

        Event::assertDispatched(ScheduledTriggerFired::class, fn (ScheduledTriggerFired $event): bool => $event->getTriggerType() === AutomationTrigger::CREDENTIAL_EXPIRED->value);
    }

    public function test_credentials_outside_the_lookahead_are_ignored(): void
    {
        $this->credentialExpiringInDays(45);
        $this->credentialExpiringInDays(-3);

        $this->runJob();

        Event::assertNotDispatched(ScheduledTriggerFired::class);
        Notification::assertNothingSent();
    }

    public function test_credential_reminders_are_sent_on_threshold_days(): void
    {
        [$thirty] = $this->credentialExpiringInDays(30);
        [$fourteen] = $this->credentialExpiringInDays(14);
        [$one] = $this->credentialExpiringInDays(1);
        [$expired] = $this->credentialExpiringInDays(0);
        [$ten] = $this->credentialExpiringInDays(10);

        $this->runJob();

        Notification::assertSentTo($thirty, CredentialExpiring::class, fn (CredentialExpiring $notification): bool => $notification->daysUntilExpiry === 30);
        Notification::assertSentTo($fourteen, CredentialExpiring::class, fn (CredentialExpiring $notification): bool => $notification->daysUntilExpiry === 14);
        Notification::assertSentTo($one, CredentialExpiring::class, fn (CredentialExpiring $notification): bool => $notification->daysUntilExpiry === 1);
        Notification::assertSentTo($expired, CredentialExpiring::class, fn (CredentialExpiring $notification): bool => $notification->daysUntilExpiry === 0);
        Notification::assertNotSentTo($ten, CredentialExpiring::class);
    }

    public function test_superseded_credentials_are_skipped(): void
    {
        [$user] = $this->credentialExpiringInDays(14, 'First Aid');

        $renewal = TrainingRecord::factory()->for($user)->createQuietly();
        $renewal->credentials()->attach(Credential::query()->where('name', 'First Aid')->firstOrFail(), [
            'expires_at' => now()->addYear(),
        ]);

        $this->runJob();

        Event::assertNotDispatched(ScheduledTriggerFired::class);
        Notification::assertNotSentTo($user, CredentialExpiring::class);
    }

    public function test_qualification_triggers_and_reminders(): void
    {
        $qualification = Qualification::factory()->createQuietly(['name' => 'Marksman']);
        $user = User::factory()->createQuietly();
        $record = QualificationRecord::factory()->for($user)->for($qualification)->createQuietly([
            'expires_at' => now()->addDays(14),
        ]);

        $this->runJob();

        Event::assertDispatched(ScheduledTriggerFired::class, function (ScheduledTriggerFired $event) use ($record): bool {
            $context = $event->getExpressionContext()->toExpressionArray();

            return $event->getTriggerType() === AutomationTrigger::QUALIFICATION_RECORD_EXPIRING->value
                && $event->getSubject()->is($record)
                && $context['model']['qualification_name'] === 'Marksman'
                && $context['model']['days_until_expiry'] === 14;
        });

        Notification::assertSentTo($user, CredentialExpiring::class, fn (CredentialExpiring $notification): bool => $notification->kind === 'qualification');
    }

    public function test_qualification_expired_trigger_fires(): void
    {
        QualificationRecord::factory()->createQuietly(['expires_at' => now()->subHour()]);

        $this->runJob();

        Event::assertDispatched(ScheduledTriggerFired::class, fn (ScheduledTriggerFired $event): bool => $event->getTriggerType() === AutomationTrigger::QUALIFICATION_RECORD_EXPIRED->value);
    }

    public function test_should_remind_thresholds(): void
    {
        $this->assertTrue(CredentialExpiring::shouldRemind(30));
        $this->assertTrue(CredentialExpiring::shouldRemind(14));
        $this->assertTrue(CredentialExpiring::shouldRemind(1));
        $this->assertTrue(CredentialExpiring::shouldRemind(0));
        $this->assertFalse(CredentialExpiring::shouldRemind(7));
        $this->assertFalse(CredentialExpiring::shouldRemind(-1));
    }

    /**
     * @return array{0: User, 1: TrainingRecordCredential}
     */
    private function credentialExpiringInDays(int $days, ?string $name = null): array
    {
        $user = User::factory()->createQuietly();
        $credential = Credential::factory()->createQuietly(array_filter(['name' => $name]));
        $trainingRecord = TrainingRecord::factory()->for($user)->createQuietly();
        $trainingRecord->credentials()->attach($credential, ['expires_at' => now()->addDays($days)]);

        return [$user, TrainingRecordCredential::query()->where('training_record_id', $trainingRecord->getKey())->firstOrFail()];
    }

    private function runJob(): void
    {
        $job = new ProcessExpiringRecords($this->tenant->getKey());
        $job->handle();
    }
}
