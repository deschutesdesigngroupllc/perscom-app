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
     *
     * @return void
     */
    public function created(CombatRecord $combat)
    {
        Notification::send($combat->user, new NewCombatRecord($combat));

        Webhook::query()->whereJsonContains('events', [WebhookEvent::COMBAT_RECORD_CREATED->value])->each(function (Webhook $webhook) use ($combat) {
            WebhookService::dispatch($webhook, WebhookEvent::COMBAT_RECORD_CREATED->value, $combat);
        });
    }

    /**
     * Handle the Combat "updated" event.
     *
     * @return void
     */
    public function updated(CombatRecord $combat)
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::COMBAT_RECORD_UPDATED->value])->each(function (Webhook $webhook) use ($combat) {
            WebhookService::dispatch($webhook, WebhookEvent::COMBAT_RECORD_UPDATED->value, $combat);
        });
    }

    /**
     * Handle the Combat "deleted" event.
     *
     * @return void
     */
    public function deleted(CombatRecord $combat)
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::COMBAT_RECORD_DELETED->value])->each(function (Webhook $webhook) use ($combat) {
            WebhookService::dispatch($webhook, WebhookEvent::COMBAT_RECORD_DELETED->value, $combat);
        });
    }

    /**
     * Handle the Combat "restored" event.
     *
     * @return void
     */
    public function restored(CombatRecord $combat)
    {
        //
    }

    /**
     * Handle the Combat "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(CombatRecord $combat)
    {
        //
    }
}
