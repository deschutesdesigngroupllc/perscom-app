<?php

namespace App\Observers;

use App\Models\Calendar;
use App\Models\Enums\WebhookEvent;
use App\Models\Webhook;
use App\Services\WebhookService;

class CalendarObserver
{
    /**
     * Handle the Calendar "created" event.
     */
    public function created(Calendar $calendar): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::CALENDAR_CREATED->value])->each(function (Webhook $webhook) use ($calendar) {
            WebhookService::dispatch($webhook, WebhookEvent::CALENDAR_CREATED->value, $calendar);
        });
    }

    /**
     * Handle the Calendar "updated" event.
     */
    public function updated(Calendar $calendar): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::CALENDAR_UPDATED->value])->each(function (Webhook $webhook) use ($calendar) {
            WebhookService::dispatch($webhook, WebhookEvent::CALENDAR_UPDATED->value, $calendar);
        });
    }

    /**
     * Handle the Calendar "deleted" event.
     */
    public function deleted(Calendar $calendar): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::CALENDAR_DELETED->value])->each(function (Webhook $webhook) use ($calendar) {
            WebhookService::dispatch($webhook, WebhookEvent::CALENDAR_DELETED->value, $calendar);
        });
    }

    /**
     * Handle the Calendar "restored" event.
     */
    public function restored(Calendar $calendar): void
    {
        //
    }

    /**
     * Handle the Calendar "force deleted" event.
     */
    public function forceDeleted(Calendar $calendar): void
    {
        //
    }
}
