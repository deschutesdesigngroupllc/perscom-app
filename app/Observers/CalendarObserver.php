<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Calendar;
use App\Models\Enums\WebhookEvent;
use App\Models\Webhook;
use App\Services\WebhookService;

class CalendarObserver
{
    public function created(Calendar $calendar): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::CALENDAR_CREATED->value])->each(function (Webhook $webhook) use ($calendar): void {
            WebhookService::dispatch($webhook, WebhookEvent::CALENDAR_CREATED->value, $calendar);
        });
    }

    public function updated(Calendar $calendar): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::CALENDAR_UPDATED->value])->each(function (Webhook $webhook) use ($calendar): void {
            WebhookService::dispatch($webhook, WebhookEvent::CALENDAR_UPDATED->value, $calendar);
        });
    }

    public function deleted(Calendar $calendar): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::CALENDAR_DELETED->value])->each(function (Webhook $webhook) use ($calendar): void {
            WebhookService::dispatch($webhook, WebhookEvent::CALENDAR_DELETED->value, $calendar);
        });
    }
}
