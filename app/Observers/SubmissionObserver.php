<?php

declare(strict_types=1);

namespace App\Observers;

use App\Jobs\Tenant\SendModelNotifications;
use App\Models\Enums\WebhookEvent;
use App\Models\Submission;
use App\Models\Webhook;
use App\Services\WebhookService;

class SubmissionObserver
{
    public function created(Submission $submission): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::SUBMISSION_CREATED->value])->each(function (Webhook $webhook) use ($submission): void {
            WebhookService::dispatch($webhook, WebhookEvent::SUBMISSION_CREATED->value, $submission);
        });

        if ($status = optional($submission->form)->submission_status) {
            $submission->statuses()->attach($status->getKey());
        }

        if (filled($submission->form)) {
            SendModelNotifications::dispatch($submission->form, 'submission.created');
        }
    }

    public function updated(Submission $submission): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::SUBMISSION_UPDATED->value])->each(function (Webhook $webhook) use ($submission): void {
            WebhookService::dispatch($webhook, WebhookEvent::SUBMISSION_UPDATED->value, $submission);
        });
    }

    public function deleted(Submission $submission): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::SUBMISSION_DELETED->value])->each(function (Webhook $webhook) use ($submission): void {
            WebhookService::dispatch($webhook, WebhookEvent::SUBMISSION_DELETED->value, $submission);
        });
    }
}
