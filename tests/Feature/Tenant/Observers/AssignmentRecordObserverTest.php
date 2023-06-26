<?php

namespace Tests\Feature\Tenant\Observers;

use App\Jobs\CallWebhook;
use App\Models\AssignmentRecord;
use App\Models\Enums\WebhookEvent;
use App\Models\Webhook;
use App\Notifications\Tenant\NewAssignmentRecord;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\Tenant\TenantTestCase;

class AssignmentRecordObserverTest extends TenantTestCase
{
    public function test_create_assignment_record_notification_sent()
    {
        Notification::fake();

        $assignment = AssignmentRecord::factory()->create();

        Notification::assertSentTo($assignment->user, NewAssignmentRecord::class);
    }

    public function test_create_assignment_record_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::ASSIGNMENT_RECORD_CREATED],
        ])->create();

        AssignmentRecord::factory()->create();

        Queue::assertPushed(CallWebhook::class);
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

        Queue::assertPushed(CallWebhook::class);
    }

    public function test_delete_assignment_record_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::ASSIGNMENT_RECORD_DELETED],
        ])->create();

        $assignment = AssignmentRecord::factory()->create();
        $assignment->delete();

        Queue::assertPushed(CallWebhook::class);
    }
}
