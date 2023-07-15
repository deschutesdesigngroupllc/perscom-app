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
    /**
     * Handle the Rank "created" event.
     */
    public function created(RankRecord $rank): void
    {
        if ($rank->user) {
            $rank->user->rank_id = $rank->rank?->id;
            $rank->user->save();
        }

        Notification::send($rank->user, new NewRankRecord($rank));

        Webhook::query()->whereJsonContains('events', [WebhookEvent::RANK_RECORD_CREATED->value])->each(function (Webhook $webhook) use ($rank) {
            WebhookService::dispatch($webhook, WebhookEvent::RANK_RECORD_CREATED->value, $rank);
        });
    }

    /**
     * Handle the Rank "updated" event.
     */
    public function updated(RankRecord $rank): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::RANK_RECORD_UPDATED->value])->each(function (Webhook $webhook) use ($rank) {
            WebhookService::dispatch($webhook, WebhookEvent::RANK_RECORD_UPDATED->value, $rank);
        });
    }

    /**
     * Handle the Rank "deleted" event.
     */
    public function deleted(RankRecord $rank): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::RANK_RECORD_DELETED->value])->each(function (Webhook $webhook) use ($rank) {
            WebhookService::dispatch($webhook, WebhookEvent::RANK_RECORD_DELETED->value, $rank);
        });
    }

    /**
     * Handle the Rank "restored" event.
     */
    public function restored(RankRecord $rank): void
    {
        //
    }

    /**
     * Handle the Rank "force deleted" event.
     */
    public function forceDeleted(RankRecord $rank): void
    {
        //
    }
}
