<?php

namespace Tests\Feature\Tenant\Observers;

use App\Jobs\CallWebhook;
use App\Models\Enums\WebhookEvent;
use App\Models\Form;
use App\Models\Submission;
use App\Models\Webhook;
use App\Notifications\Tenant\NewSubmission;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\Tenant\TenantTestCase;

class SubmissionObserverTest extends TenantTestCase
{
    public function test_notification_is_sent()
    {
        Notification::fake();

        $form = Form::factory()->create();
        $form->notifications()->attach($this->user);

        Submission::factory()->for($form)->create();

        Notification::assertSentTo($this->user, NewSubmission::class);
    }

    public function test_create_submission_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::SUBMISSION_CREATED],
        ])->create();

        Submission::factory()->create();

        Queue::assertPushed(CallWebhook::class);
    }

    public function test_update_submission_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::SUBMISSION_UPDATED],
        ])->create();

        $submission = Submission::factory()->create();
        $submission->update([
            'text' => 'foo bar',
        ]);

        Queue::assertPushed(CallWebhook::class);
    }

    public function test_delete_submission_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::SUBMISSION_DELETED],
        ])->create();

        $submission = Submission::factory()->create();
        $submission->delete();

        Queue::assertPushed(CallWebhook::class);
    }
}
