<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Observers;

use App\Events\Automations\RecordCreated;
use App\Events\Automations\RecordDeleted;
use App\Events\Automations\RecordUpdated;
use App\Models\Enums\AutomationTrigger;
use App\Models\Enums\WebhookEvent;
use App\Models\TrainingRecord;
use App\Models\User;
use App\Models\Webhook;
use App\Notifications\Tenant\NewTrainingRecord;
use App\Observers\TrainingRecordObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Spatie\WebhookServer\CallWebhookJob;
use Tests\Feature\Tenant\TenantTestCase;

class TrainingRecordObserverTest extends TenantTestCase
{
    public function test_observer_is_registered_for_training_record_lifecycle(): void
    {
        $observer = Mockery::spy(TrainingRecordObserver::class);
        $this->app->instance(TrainingRecordObserver::class, $observer);

        $training = TrainingRecord::factory()->create();
        $training->update(['text' => 'foo bar']);
        $training->delete();

        $observer->shouldHaveReceived('created')->once();
        $observer->shouldHaveReceived('updated')->once();
        $observer->shouldHaveReceived('deleted')->once();
    }

    public function test_create_training_record_notification_sent(): void
    {
        Notification::fake();

        $training = TrainingRecord::factory()->for($user = User::factory()->create())->create();

        Notification::assertSentTo($user, NewTrainingRecord::class, function (NewTrainingRecord $notification, iterable $channels) use ($training): true {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($training->user);
            $mail->assertTo($training->user->email);

            return true;
        });
    }

    public function test_create_training_record_webhook_sent(): void
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::TRAINING_RECORD_CREATED],
        ])->create();

        TrainingRecord::factory()->create();

        Queue::assertPushed(CallWebhookJob::class);
    }

    public function test_update_training_record_webhook_sent(): void
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::TRAINING_RECORD_UPDATED],
        ])->create();

        $training = TrainingRecord::factory()->create();
        $training->update([
            'text' => 'foo bar',
        ]);

        Queue::assertPushed(CallWebhookJob::class);
    }

    public function test_delete_training_record_webhook_sent(): void
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::TRAINING_RECORD_DELETED],
        ])->create();

        $training = TrainingRecord::factory()->create();
        $training->delete();

        Queue::assertPushed(CallWebhookJob::class);
    }

    public function test_training_record_lifecycle_dispatches_automation_events(): void
    {
        Event::fake([RecordCreated::class, RecordUpdated::class, RecordDeleted::class]);

        $training = TrainingRecord::factory()->create();
        $training->update(['text' => 'foo bar']);
        $training->delete();

        Event::assertDispatched(RecordCreated::class, fn (RecordCreated $event): bool => $event->getTriggerType() === AutomationTrigger::TRAINING_RECORD_CREATED->value);
        Event::assertDispatched(RecordUpdated::class, fn (RecordUpdated $event): bool => $event->getTriggerType() === AutomationTrigger::TRAINING_RECORD_UPDATED->value);
        Event::assertDispatched(RecordDeleted::class, fn (RecordDeleted $event): bool => $event->getTriggerType() === AutomationTrigger::TRAINING_RECORD_DELETED->value);
    }
}
