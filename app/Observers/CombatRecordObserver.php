<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\CombatRecord;
use App\Models\Enums\AutomationTrigger;
use App\Models\Enums\WebhookEvent;
use App\Models\Webhook;
use App\Notifications\Tenant\NewCombatRecord;
use App\Services\WebhookService;
use App\Traits\DispatchesAutomationEvents;
use Illuminate\Support\Facades\Notification;

class CombatRecordObserver
{
    use DispatchesAutomationEvents;

    public function created(CombatRecord $combat): void
    {
        Notification::send($combat->user, new NewCombatRecord($combat));

        Webhook::query()->whereJsonContains('events', [WebhookEvent::COMBAT_RECORD_CREATED->value])->each(function (Webhook $webhook) use ($combat): void {
            WebhookService::dispatch($webhook, WebhookEvent::COMBAT_RECORD_CREATED->value, $combat);
        });

        $this->dispatchAutomationCreated($combat, AutomationTrigger::COMBAT_RECORD_CREATED);
    }

    public function updated(CombatRecord $combat): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::COMBAT_RECORD_UPDATED->value])->each(function (Webhook $webhook) use ($combat): void {
            WebhookService::dispatch($webhook, WebhookEvent::COMBAT_RECORD_UPDATED->value, $combat);
        });

        $this->dispatchAutomationUpdated($combat, AutomationTrigger::COMBAT_RECORD_UPDATED);
    }

    public function deleted(CombatRecord $combat): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::COMBAT_RECORD_DELETED->value])->each(function (Webhook $webhook) use ($combat): void {
            WebhookService::dispatch($webhook, WebhookEvent::COMBAT_RECORD_DELETED->value, $combat);
        });

        $this->dispatchAutomationDeleted($combat, AutomationTrigger::COMBAT_RECORD_DELETED);
    }
}
