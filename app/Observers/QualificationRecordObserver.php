<?php

namespace App\Observers;

use App\Models\Enums\WebhookEvent;
use App\Models\QualificationRecord;
use App\Models\Webhook;
use App\Notifications\Tenant\NewQualificationRecord;
use App\Services\WebhookService;
use Illuminate\Support\Facades\Notification;

class QualificationRecordObserver
{
    /**
     * Handle the Qualification "created" event.
     *
     * @return void
     */
    public function created(QualificationRecord $qualification)
    {
        Notification::send($qualification->user, new NewQualificationRecord($qualification));

        Webhook::query()->whereJsonContains('events', [WebhookEvent::QUALIFICATION_RECORD_CREATED->value])->each(function (Webhook $webhook) use ($qualification) {
            WebhookService::dispatch($webhook, WebhookEvent::QUALIFICATION_RECORD_CREATED->value, $qualification);
        });
    }

    /**
     * Handle the Qualification "updated" event.
     *
     * @return void
     */
    public function updated(QualificationRecord $qualification)
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::QUALIFICATION_RECORD_UPDATED->value])->each(function (Webhook $webhook) use ($qualification) {
            WebhookService::dispatch($webhook, WebhookEvent::QUALIFICATION_RECORD_UPDATED->value, $qualification);
        });
    }

    /**
     * Handle the Qualification "deleted" event.
     *
     * @return void
     */
    public function deleted(QualificationRecord $qualification)
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::QUALIFICATION_RECORD_DELETED->value])->each(function (Webhook $webhook) use ($qualification) {
            WebhookService::dispatch($webhook, WebhookEvent::QUALIFICATION_RECORD_DELETED->value, $qualification);
        });
    }
}
