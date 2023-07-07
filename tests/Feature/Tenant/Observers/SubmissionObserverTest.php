<?php

namespace Tests\Feature\Tenant\Observers;

use App\Jobs\CallWebhook;
use App\Models\Enums\WebhookEvent;
use App\Models\Form;
use App\Models\Status;
use App\Models\Submission;
use App\Models\Webhook;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\Tenant\TenantTestCase;

class SubmissionObserverTest extends TenantTestCase
{
    public function test_default_submission_status_is_attached()
    {
        $status = Status::factory()->create();
        $form = Form::factory()->for($status, 'submission_status')->create();
        $submission = Submission::factory()->for($form)->create();

        $this->assertEquals($status->name, $submission->statuses()->first()->name);
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
