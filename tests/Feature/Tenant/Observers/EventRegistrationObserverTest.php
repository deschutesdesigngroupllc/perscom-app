<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Observers;

use App\Events\Automations\RecordCreated;
use App\Events\Automations\RecordUpdated;
use App\Models\Enums\AutomationTrigger;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Enums\WebhookEvent;
use App\Models\Event;
use App\Models\User;
use App\Models\Webhook;
use Illuminate\Support\Facades\Event as EventDispatcher;
use Illuminate\Support\Facades\Queue;
use Spatie\WebhookServer\CallWebhookJob;
use Tests\Feature\Tenant\TenantTestCase;

class EventRegistrationObserverTest extends TenantTestCase
{
    public function test_attended_status_dispatches_attended_trigger_on_update(): void
    {
        EventDispatcher::fake([RecordCreated::class, RecordUpdated::class]);

        [$event, $user] = $this->registeredUser();

        $event->registrations()->updateExistingPivot($user->getKey(), ['status' => EventRegistrationStatus::ATTENDED]);

        EventDispatcher::assertDispatched(RecordUpdated::class, fn (RecordUpdated $automationEvent): bool => $automationEvent->getTriggerType() === AutomationTrigger::EVENT_REGISTRATION_ATTENDED->value);
    }

    public function test_no_show_status_dispatches_no_show_trigger_on_create(): void
    {
        EventDispatcher::fake([RecordCreated::class, RecordUpdated::class]);

        $event = Event::factory()->createQuietly(['registration_enabled' => true]);
        $user = User::factory()->createQuietly();

        $event->registrations()->attach($user->getKey(), ['status' => EventRegistrationStatus::NO_SHOW]);

        EventDispatcher::assertDispatched(RecordCreated::class, fn (RecordCreated $automationEvent): bool => $automationEvent->getTriggerType() === AutomationTrigger::EVENT_REGISTRATION_NO_SHOW->value);
    }

    public function test_non_attendance_status_does_not_dispatch_triggers(): void
    {
        EventDispatcher::fake([RecordCreated::class, RecordUpdated::class]);

        [$event, $user] = $this->registeredUser();

        $event->registrations()->updateExistingPivot($user->getKey(), ['status' => EventRegistrationStatus::TENTATIVE]);

        EventDispatcher::assertNotDispatched(RecordCreated::class);
        EventDispatcher::assertNotDispatched(RecordUpdated::class);
    }

    public function test_attended_status_sends_webhook(): void
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::EVENT_REGISTRATION_ATTENDED],
        ])->create();

        [$event, $user] = $this->registeredUser();

        $event->registrations()->updateExistingPivot($user->getKey(), ['status' => EventRegistrationStatus::ATTENDED]);

        Queue::assertPushed(CallWebhookJob::class);
    }

    public function test_attended_status_creates_training_record_when_event_requires_it(): void
    {
        [$event, $user] = $this->registeredUser(createsTrainingRecord: true);

        $event->registrations()->updateExistingPivot($user->getKey(), ['status' => EventRegistrationStatus::ATTENDED]);

        $this->assertDatabaseHas('records_trainings', [
            'user_id' => $user->getKey(),
            'event_id' => $event->getKey(),
        ]);
    }

    /**
     * @return array{0: Event, 1: User}
     */
    private function registeredUser(bool $createsTrainingRecord = false): array
    {
        $event = Event::factory()->createQuietly([
            'registration_enabled' => true,
            'registration_deadline' => null,
            'creates_training_record' => $createsTrainingRecord,
        ]);
        $user = User::factory()->createQuietly();

        $event->registrations()->attach($user->getKey(), ['status' => EventRegistrationStatus::GOING]);

        return [$event, $user];
    }
}
