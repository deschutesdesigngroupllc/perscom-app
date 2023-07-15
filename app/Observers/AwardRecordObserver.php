<?php

namespace App\Observers;

use App\Models\AwardRecord;
use App\Models\Enums\WebhookEvent;
use App\Models\Webhook;
use App\Notifications\Tenant\NewAwardRecord;
use App\Services\WebhookService;
use Illuminate\Support\Facades\Notification;

class AwardRecordObserver
{
    /**
     * Handle the Award "created" event.
     */
    public function created(AwardRecord $award): void
    {
        Notification::send($award->user, new NewAwardRecord($award));

        Webhook::query()->whereJsonContains('events', [WebhookEvent::AWARD_RECORD_CREATED->value])->each(function (Webhook $webhook) use ($award) {
            WebhookService::dispatch($webhook, WebhookEvent::AWARD_RECORD_CREATED->value, $award);
        });
    }

    /**
     * Handle the Award "updated" event.
     */
    public function updated(AwardRecord $award): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::AWARD_RECORD_UPDATED->value])->each(function (Webhook $webhook) use ($award) {
            WebhookService::dispatch($webhook, WebhookEvent::AWARD_RECORD_UPDATED->value, $award);
        });
    }

    /**
     * Handle the Award "deleted" event.
     */
    public function deleted(AwardRecord $award): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::AWARD_RECORD_DELETED->value])->each(function (Webhook $webhook) use ($award) {
            WebhookService::dispatch($webhook, WebhookEvent::AWARD_RECORD_DELETED->value, $award);
        });
    }

    /**
     * Handle the Award "restored" event.
     */
    public function restored(AwardRecord $award): void
    {
        //
    }

    /**
     * Handle the Award "force deleted" event.
     */
    public function forceDeleted(AwardRecord $award): void
    {
        //
    }
}
