<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Observers;

use App\Models\AssignmentRecord;
use App\Models\Enums\WebhookEvent;
use App\Models\User;
use App\Models\Webhook;
use App\Notifications\Tenant\NewAssignmentRecord;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Spatie\WebhookServer\CallWebhookJob;
use Tests\Feature\Tenant\TenantTestCase;

class AssignmentRecordObserverTest extends TenantTestCase
{
    public function test_create_assignment_record_notification_sent(): void
    {
        Notification::fake();

        $assignment = AssignmentRecord::factory()->for($user = User::factory()->create())->create();

        Notification::assertSentTo($user, NewAssignmentRecord::class, function (NewAssignmentRecord $notification, iterable $channels) use ($assignment): true {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($assignment->user);
            $mail->assertTo($assignment->user->email);

            return true;
        });
    }

    public function test_create_assignment_record_webhook_sent(): void
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::ASSIGNMENT_RECORD_CREATED],
        ])->create();

        AssignmentRecord::factory()->create();

        Queue::assertPushed(CallWebhookJob::class);
    }

    public function test_update_assignment_record_webhook_sent(): void
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

    public function test_delete_assignment_record_webhook_sent(): void
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
