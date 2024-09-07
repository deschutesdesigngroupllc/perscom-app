<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Enums\WebhookEvent;
use App\Models\Event;
use App\Models\Webhook;
use App\Services\WebhookService;

class EventObserver
{
    public function created(Event $event): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::EVENT_CREATED->value])->each(function (Webhook $webhook) use ($event) {
            WebhookService::dispatch($webhook, WebhookEvent::EVENT_CREATED->value, $event);
        });
    }

    public function updated(Event $event): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::EVENT_UPDATED->value])->each(function (Webhook $webhook) use ($event) {
            WebhookService::dispatch($webhook, WebhookEvent::EVENT_UPDATED->value, $event);
        });
    }

    public function deleted(Event $event): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::EVENT_DELETED->value])->each(function (Webhook $webhook) use ($event) {
            WebhookService::dispatch($webhook, WebhookEvent::EVENT_DELETED->value, $event);
        });
    }
}
