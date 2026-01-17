<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\AwardRecord;
use App\Models\Enums\AutomationTrigger;
use App\Models\Enums\WebhookEvent;
use App\Models\Webhook;
use App\Notifications\Tenant\NewAwardRecord;
use App\Services\WebhookService;
use App\Traits\DispatchesAutomationEvents;
use Illuminate\Support\Facades\Notification;

class AwardRecordObserver
{
    use DispatchesAutomationEvents;

    public function created(AwardRecord $award): void
    {
        Notification::send($award->user, new NewAwardRecord($award));

        Webhook::query()->whereJsonContains('events', [WebhookEvent::AWARD_RECORD_CREATED->value])->each(function (Webhook $webhook) use ($award): void {
            WebhookService::dispatch($webhook, WebhookEvent::AWARD_RECORD_CREATED->value, $award);
        });

        $this->dispatchAutomationCreated($award, AutomationTrigger::AWARD_RECORD_CREATED);
    }

    public function updated(AwardRecord $award): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::AWARD_RECORD_UPDATED->value])->each(function (Webhook $webhook) use ($award): void {
            WebhookService::dispatch($webhook, WebhookEvent::AWARD_RECORD_UPDATED->value, $award);
        });

        $this->dispatchAutomationUpdated($award, AutomationTrigger::AWARD_RECORD_UPDATED);
    }

    public function deleted(AwardRecord $award): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::AWARD_RECORD_DELETED->value])->each(function (Webhook $webhook) use ($award): void {
            WebhookService::dispatch($webhook, WebhookEvent::AWARD_RECORD_DELETED->value, $award);
        });

        $this->dispatchAutomationDeleted($award, AutomationTrigger::AWARD_RECORD_DELETED);
    }
}
