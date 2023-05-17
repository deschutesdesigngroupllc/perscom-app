<?php

namespace App\Observers;

use App\Models\Enums\WebhookEvent;
use App\Models\Event;
use App\Models\Webhook;
use App\Services\WebhookService;

class EventObserver
{
    /**
     * Handle the Event "created" event.
     */
    public function created(Event $event): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::EVENT_CREATED->value])->each(function (Webhook $webhook) use ($event) {
            WebhookService::dispatch($webhook, WebhookEvent::EVENT_CREATED->value, $event);
        });
    }

    /**
     * Handle the Event "updated" event.
     */
    public function updated(Event $event): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::EVENT_UPDATED->value])->each(function (Webhook $webhook) use ($event) {
            WebhookService::dispatch($webhook, WebhookEvent::EVENT_UPDATED->value, $event);
        });
    }

    /**
     * Handle the Event "deleted" event.
     */
    public function deleted(Event $event): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::EVENT_DELETED->value])->each(function (Webhook $webhook) use ($event) {
            WebhookService::dispatch($webhook, WebhookEvent::EVENT_DELETED->value, $event);
        });
    }

    /**
     * Handle the Event "restored" event.
     */
    public function restored(Event $event): void
    {
        //
    }

    /**
     * Handle the Event "force deleted" event.
     */
    public function forceDeleted(Event $event): void
    {
        //
    }
}
