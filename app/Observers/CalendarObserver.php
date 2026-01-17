<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Calendar;
use App\Models\Enums\AutomationTrigger;
use App\Models\Enums\WebhookEvent;
use App\Models\Webhook;
use App\Services\WebhookService;
use App\Traits\DispatchesAutomationEvents;

class CalendarObserver
{
    use DispatchesAutomationEvents;

    public function created(Calendar $calendar): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::CALENDAR_CREATED->value])->each(function (Webhook $webhook) use ($calendar): void {
            WebhookService::dispatch($webhook, WebhookEvent::CALENDAR_CREATED->value, $calendar);
        });

        $this->dispatchAutomationCreated($calendar, AutomationTrigger::CALENDAR_CREATED);
    }

    public function updated(Calendar $calendar): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::CALENDAR_UPDATED->value])->each(function (Webhook $webhook) use ($calendar): void {
            WebhookService::dispatch($webhook, WebhookEvent::CALENDAR_UPDATED->value, $calendar);
        });

        $this->dispatchAutomationUpdated($calendar, AutomationTrigger::CALENDAR_UPDATED);
    }

    public function deleted(Calendar $calendar): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::CALENDAR_DELETED->value])->each(function (Webhook $webhook) use ($calendar): void {
            WebhookService::dispatch($webhook, WebhookEvent::CALENDAR_DELETED->value, $calendar);
        });

        $this->dispatchAutomationDeleted($calendar, AutomationTrigger::CALENDAR_DELETED);
    }
}
