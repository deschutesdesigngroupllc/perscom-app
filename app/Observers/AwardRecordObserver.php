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
     *
     * @return void
     */
    public function created(AwardRecord $award)
    {
        Notification::send($award->user, new NewAwardRecord($award));

        Webhook::query()->whereJsonContains('events', [WebhookEvent::AWARD_RECORD_CREATED->value])->each(function (Webhook $webhook) use ($award) {
            WebhookService::dispatch($webhook, WebhookEvent::AWARD_RECORD_CREATED->value, $award);
        });
    }

    /**
     * Handle the Award "updated" event.
     *
     * @return void
     */
    public function updated(AwardRecord $award)
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::AWARD_RECORD_UPDATED->value])->each(function (Webhook $webhook) use ($award) {
            WebhookService::dispatch($webhook, WebhookEvent::AWARD_RECORD_UPDATED->value, $award);
        });
    }

    /**
     * Handle the Award "deleted" event.
     *
     * @return void
     */
    public function deleted(AwardRecord $award)
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::AWARD_RECORD_DELETED->value])->each(function (Webhook $webhook) use ($award) {
            WebhookService::dispatch($webhook, WebhookEvent::AWARD_RECORD_DELETED->value, $award);
        });
    }
}
