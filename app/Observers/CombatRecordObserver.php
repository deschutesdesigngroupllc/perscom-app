<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\CombatRecord;
use App\Models\Enums\WebhookEvent;
use App\Models\Webhook;
use App\Notifications\Tenant\NewCombatRecord;
use App\Services\WebhookService;
use Illuminate\Support\Facades\Notification;

class CombatRecordObserver
{
    public function created(CombatRecord $combat): void
    {
        Notification::send($combat->user, new NewCombatRecord($combat));

        Webhook::query()->whereJsonContains('events', [WebhookEvent::COMBAT_RECORD_CREATED->value])->each(function (Webhook $webhook) use ($combat): void {
            WebhookService::dispatch($webhook, WebhookEvent::COMBAT_RECORD_CREATED->value, $combat);
        });
    }

    public function updated(CombatRecord $combat): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::COMBAT_RECORD_UPDATED->value])->each(function (Webhook $webhook) use ($combat): void {
            WebhookService::dispatch($webhook, WebhookEvent::COMBAT_RECORD_UPDATED->value, $combat);
        });
    }

    public function deleted(CombatRecord $combat): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::COMBAT_RECORD_DELETED->value])->each(function (Webhook $webhook) use ($combat): void {
            WebhookService::dispatch($webhook, WebhookEvent::COMBAT_RECORD_DELETED->value, $combat);
        });
    }
}
