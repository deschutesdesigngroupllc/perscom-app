<?php

namespace App\Observers;

use App\Models\Enums\WebhookEvent;
use App\Models\RankRecord;
use App\Models\Webhook;
use App\Notifications\Tenant\NewRankRecord;
use App\Services\WebhookService;
use Illuminate\Support\Facades\Notification;

class RankRecordObserver
{
    public function created(RankRecord $rank): void
    {
        if ($rank->user) {
            $rank->user->rank_id = optional($rank->rank)->id;
            $rank->user->save();
        }

        Notification::send($rank->user, new NewRankRecord($rank));

        Webhook::query()->whereJsonContains('events', [WebhookEvent::RANK_RECORD_CREATED->value])->each(function (Webhook $webhook) use ($rank) {
            WebhookService::dispatch($webhook, WebhookEvent::RANK_RECORD_CREATED->value, $rank);
        });
    }

    public function updated(RankRecord $rank): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::RANK_RECORD_UPDATED->value])->each(function (Webhook $webhook) use ($rank) {
            WebhookService::dispatch($webhook, WebhookEvent::RANK_RECORD_UPDATED->value, $rank);
        });
    }

    public function deleted(RankRecord $rank): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::RANK_RECORD_DELETED->value])->each(function (Webhook $webhook) use ($rank) {
            WebhookService::dispatch($webhook, WebhookEvent::RANK_RECORD_DELETED->value, $rank);
        });
    }
}
