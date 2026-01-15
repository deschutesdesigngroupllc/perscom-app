<?php

declare(strict_types=1);

namespace App\Observers;

use App\Actions\Events\SendUpcomingEventNotification;
use App\Models\Enums\AutomationTrigger;
use App\Models\Enums\WebhookEvent;
use App\Models\Event;
use App\Models\Webhook;
use App\Services\WebhookService;
use App\Traits\DispatchesAutomationEvents;
use Throwable;

class EventObserver
{
    use DispatchesAutomationEvents;

    /**
     * @throws Throwable
     */
    public function created(Event $event): void
    {
        //        if (SendUpcomingEventNotification::canSendNotifications($event)) {
        //            defer(fn () => collect($event->notifications_interval)->each( fn ($interval) => SendUpcomingEventNotification::handle($event, $interval)));
        //        }

        Webhook::query()->whereJsonContains('events', [WebhookEvent::EVENT_CREATED->value])->each(function (Webhook $webhook) use ($event): void {
            WebhookService::dispatch($webhook, WebhookEvent::EVENT_CREATED->value, $event);
        });

        $this->dispatchAutomationCreated($event, AutomationTrigger::EVENT_CREATED);
    }

    public function updated(Event $event): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::EVENT_UPDATED->value])->each(function (Webhook $webhook) use ($event): void {
            WebhookService::dispatch($webhook, WebhookEvent::EVENT_UPDATED->value, $event);
        });

        $this->dispatchAutomationUpdated($event, AutomationTrigger::EVENT_UPDATED);
    }

    public function deleted(Event $event): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::EVENT_DELETED->value])->each(function (Webhook $webhook) use ($event): void {
            WebhookService::dispatch($webhook, WebhookEvent::EVENT_DELETED->value, $event);
        });

        $this->dispatchAutomationDeleted($event, AutomationTrigger::EVENT_DELETED);
    }
}
