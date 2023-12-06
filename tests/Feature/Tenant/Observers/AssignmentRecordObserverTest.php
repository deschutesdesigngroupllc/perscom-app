<?php

namespace Tests\Feature\Tenant\Observers;

use App\Jobs\GenerateOpenAiNewsfeedContent;
use App\Models\AssignmentRecord;
use App\Models\Enums\WebhookEvent;
use App\Models\Webhook;
use App\Notifications\Tenant\NewAssignmentRecord;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Spatie\WebhookServer\CallWebhookJob;
use Tests\Feature\Tenant\TenantTestCase;

class AssignmentRecordObserverTest extends TenantTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Queue::fake([GenerateOpenAiNewsfeedContent::class]);
    }

    public function test_create_assignment_record_assigns_user_properties(): void
    {
        $this->assertNull($this->user->unit);
        $this->assertNull($this->user->position);
        $this->assertNull($this->user->specialty);

        $assignment = AssignmentRecord::factory()->for($this->user)->create();

        $user = $this->user->fresh();

        $this->assertSame($assignment->unit->getKey(), $user->unit->getKey());
        $this->assertSame($assignment->position->getKey(), $user->position->getKey());
        $this->assertSame($assignment->specialty->getKey(), $user->specialty->getKey());
    }

    public function test_create_assignment_record_notification_sent()
    {
        Notification::fake();

        $assignment = AssignmentRecord::factory()->for($this->user)->create();

        Notification::assertSentTo($this->user, NewAssignmentRecord::class, function ($notification, $channels) use ($assignment) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($assignment->user);
            $mail->assertTo($assignment->user->email);

            $nova = $notification->toNova();
            $this->assertSame('A new assignment record has been added to your personnel file.', $nova->message);

            return true;
        });
    }

    public function test_create_assignment_record_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::ASSIGNMENT_RECORD_CREATED],
        ])->create();

        AssignmentRecord::factory()->create();

        Queue::assertPushed(CallWebhookJob::class);
    }

    public function test_update_assignment_record_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::ASSIGNMENT_RECORD_UPDATED],
        ])->create();

        $assignment = AssignmentRecord::factory()->create();
        $assignment->update([
            'text' => 'foo bar',
        ]);

        Queue::assertPushed(CallWebhookJob::class);
    }

    public function test_delete_assignment_record_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::ASSIGNMENT_RECORD_DELETED],
        ])->create();

        $assignment = AssignmentRecord::factory()->create();
        $assignment->delete();

        Queue::assertPushed(CallWebhookJob::class);
    }
}
