<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Enums\AutomationTrigger;
use App\Models\Enums\WebhookEvent;
use App\Models\QualificationRecord;
use App\Models\Webhook;
use App\Notifications\Tenant\NewQualificationRecord;
use App\Services\WebhookService;
use App\Traits\DispatchesAutomationEvents;
use Illuminate\Support\Facades\Notification;

class QualificationRecordObserver
{
    use DispatchesAutomationEvents;

    public function created(QualificationRecord $qualification): void
    {
        Notification::send($qualification->user, new NewQualificationRecord($qualification));

        Webhook::query()->whereJsonContains('events', [WebhookEvent::QUALIFICATION_RECORD_CREATED->value])->each(function (Webhook $webhook) use ($qualification): void {
            WebhookService::dispatch($webhook, WebhookEvent::QUALIFICATION_RECORD_CREATED->value, $qualification);
        });

        $this->dispatchAutomationCreated($qualification, AutomationTrigger::QUALIFICATION_RECORD_CREATED);
    }

    public function updated(QualificationRecord $qualification): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::QUALIFICATION_RECORD_UPDATED->value])->each(function (Webhook $webhook) use ($qualification): void {
            WebhookService::dispatch($webhook, WebhookEvent::QUALIFICATION_RECORD_UPDATED->value, $qualification);
        });

        $this->dispatchAutomationUpdated($qualification, AutomationTrigger::QUALIFICATION_RECORD_UPDATED);
    }

    public function deleted(QualificationRecord $qualification): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::QUALIFICATION_RECORD_DELETED->value])->each(function (Webhook $webhook) use ($qualification): void {
            WebhookService::dispatch($webhook, WebhookEvent::QUALIFICATION_RECORD_DELETED->value, $qualification);
        });

        $this->dispatchAutomationDeleted($qualification, AutomationTrigger::QUALIFICATION_RECORD_DELETED);
    }
}
