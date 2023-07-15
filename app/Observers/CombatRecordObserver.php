<?php

namespace App\Observers;

use App\Models\CombatRecord;
use App\Models\Enums\WebhookEvent;
use App\Models\Webhook;
use App\Notifications\Tenant\NewCombatRecord;
use App\Services\WebhookService;
use Illuminate\Support\Facades\Notification;

class CombatRecordObserver
{
    /**
     * Handle the Combat "created" event.
     */
    public function created(CombatRecord $combat): void
    {
        Notification::send($combat->user, new NewCombatRecord($combat));

        Webhook::query()->whereJsonContains('events', [WebhookEvent::COMBAT_RECORD_CREATED->value])->each(function (Webhook $webhook) use ($combat) {
            WebhookService::dispatch($webhook, WebhookEvent::COMBAT_RECORD_CREATED->value, $combat);
        });
    }

    /**
     * Handle the Combat "updated" event.
     */
    public function updated(CombatRecord $combat): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::COMBAT_RECORD_UPDATED->value])->each(function (Webhook $webhook) use ($combat) {
            WebhookService::dispatch($webhook, WebhookEvent::COMBAT_RECORD_UPDATED->value, $combat);
        });
    }

    /**
     * Handle the Combat "deleted" event.
     */
    public function deleted(CombatRecord $combat): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::COMBAT_RECORD_DELETED->value])->each(function (Webhook $webhook) use ($combat) {
            WebhookService::dispatch($webhook, WebhookEvent::COMBAT_RECORD_DELETED->value, $combat);
        });
    }

    /**
     * Handle the Combat "restored" event.
     */
    public function restored(CombatRecord $combat): void
    {
        //
    }

    /**
     * Handle the Combat "force deleted" event.
     */
    public function forceDeleted(CombatRecord $combat): void
    {
        //
    }
}
