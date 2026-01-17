<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Enums\AutomationTrigger;
use App\Models\Enums\WebhookEvent;
use App\Models\RankRecord;
use App\Models\Webhook;
use App\Notifications\Tenant\NewRankRecord;
use App\Services\WebhookService;
use App\Traits\DispatchesAutomationEvents;
use Illuminate\Support\Facades\Notification;

class RankRecordObserver
{
    use DispatchesAutomationEvents;

    public function created(RankRecord $rank): void
    {
        if ($rank->user) {
            $rank->user->update([
                'rank_id' => $rank->rank_id,
            ]);
        }

        Notification::send($rank->user, new NewRankRecord($rank));

        Webhook::query()->whereJsonContains('events', [WebhookEvent::RANK_RECORD_CREATED->value])->each(function (Webhook $webhook) use ($rank): void {
            WebhookService::dispatch($webhook, WebhookEvent::RANK_RECORD_CREATED->value, $rank);
        });

        $this->dispatchAutomationCreated($rank, AutomationTrigger::RANK_RECORD_CREATED);
    }

    public function updated(RankRecord $rank): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::RANK_RECORD_UPDATED->value])->each(function (Webhook $webhook) use ($rank): void {
            WebhookService::dispatch($webhook, WebhookEvent::RANK_RECORD_UPDATED->value, $rank);
        });

        $this->dispatchAutomationUpdated($rank, AutomationTrigger::RANK_RECORD_UPDATED);
    }

    public function deleted(RankRecord $rank): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::RANK_RECORD_DELETED->value])->each(function (Webhook $webhook) use ($rank): void {
            WebhookService::dispatch($webhook, WebhookEvent::RANK_RECORD_DELETED->value, $rank);
        });

        $this->dispatchAutomationDeleted($rank, AutomationTrigger::RANK_RECORD_DELETED);
    }
}
