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
    public function created(AwardRecord $award): void
    {
        Notification::send($award->user, new NewAwardRecord($award));

        Webhook::query()->whereJsonContains('events', [WebhookEvent::AWARD_RECORD_CREATED->value])->each(function (Webhook $webhook) use ($award) {
            WebhookService::dispatch($webhook, WebhookEvent::AWARD_RECORD_CREATED->value, $award);
        });
    }

    public function updated(AwardRecord $award): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::AWARD_RECORD_UPDATED->value])->each(function (Webhook $webhook) use ($award) {
            WebhookService::dispatch($webhook, WebhookEvent::AWARD_RECORD_UPDATED->value, $award);
        });
    }

    public function deleted(AwardRecord $award): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::AWARD_RECORD_DELETED->value])->each(function (Webhook $webhook) use ($award) {
            WebhookService::dispatch($webhook, WebhookEvent::AWARD_RECORD_DELETED->value, $award);
        });
    }
}
