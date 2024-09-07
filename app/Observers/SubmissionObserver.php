<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Enums\WebhookEvent;
use App\Models\Submission;
use App\Models\Webhook;
use App\Notifications\Tenant\NewSubmission;
use App\Services\WebhookService;
use Illuminate\Support\Facades\Notification;

class SubmissionObserver
{
    public function created(Submission $submission): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::SUBMISSION_CREATED->value])->each(function (Webhook $webhook) use ($submission) {
            WebhookService::dispatch($webhook, WebhookEvent::SUBMISSION_CREATED->value, $submission);
        });

        if ($status = optional($submission->form)->submission_status) {
            $submission->statuses()->attach($status->getKey());
        }

        Notification::send(optional($submission->form)->notifications, new NewSubmission($submission));
    }

    public function updated(Submission $submission): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::SUBMISSION_UPDATED->value])->each(function (Webhook $webhook) use ($submission) {
            WebhookService::dispatch($webhook, WebhookEvent::SUBMISSION_UPDATED->value, $submission);
        });
    }

    public function deleted(Submission $submission): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::SUBMISSION_DELETED->value])->each(function (Webhook $webhook) use ($submission) {
            WebhookService::dispatch($webhook, WebhookEvent::SUBMISSION_DELETED->value, $submission);
        });
    }
}
