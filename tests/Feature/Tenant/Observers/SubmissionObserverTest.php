<?php

namespace Tests\Feature\Tenant\Observers;

use Spatie\WebhookServer\CallWebhookJob;
use App\Models\Enums\WebhookEvent;
use App\Models\Form;
use App\Models\Status;
use App\Models\Submission;
use App\Models\Webhook;
use App\Notifications\Tenant\NewSubmission;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\Tenant\TenantTestCase;

class SubmissionObserverTest extends TenantTestCase
{
    public function test_create_submission_notification_is_sent()
    {
        Notification::fake();

        $form = Form::factory()->create();
        $form->notifications()->attach($this->user);

        $submission = Submission::factory()->for($form)->create();

        Notification::assertSentTo($this->user, NewSubmission::class, function ($notification, $channels) use ($submission) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($submission->user);
            $mail->assertTo($submission->user->email);

            $nova = $notification->toNova();
            $this->assertSame("A new {$submission->form->name} has been submitted.", $nova->message);

            return true;
        });
    }

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

        Queue::assertPushed(CallWebhookJob::class);
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

        Queue::assertPushed(CallWebhookJob::class);
    }

    public function test_delete_submission_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::SUBMISSION_DELETED],
        ])->create();

        $submission = Submission::factory()->create();
        $submission->delete();

        Queue::assertPushed(CallWebhookJob::class);
    }
}
