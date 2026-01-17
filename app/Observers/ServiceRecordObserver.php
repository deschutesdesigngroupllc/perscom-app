<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Enums\AutomationTrigger;
use App\Models\Enums\WebhookEvent;
use App\Models\ServiceRecord;
use App\Models\Webhook;
use App\Notifications\Tenant\NewServiceRecord;
use App\Services\WebhookService;
use App\Traits\DispatchesAutomationEvents;
use Illuminate\Support\Facades\Notification;

class ServiceRecordObserver
{
    use DispatchesAutomationEvents;

    public function created(ServiceRecord $service): void
    {
        Notification::send($service->user, new NewServiceRecord($service));

        Webhook::query()->whereJsonContains('events', [WebhookEvent::SERVICE_RECORD_CREATED->value])->each(function (Webhook $webhook) use ($service): void {
            WebhookService::dispatch($webhook, WebhookEvent::SERVICE_RECORD_CREATED->value, $service);
        });

        $this->dispatchAutomationCreated($service, AutomationTrigger::SERVICE_RECORD_CREATED);
    }

    public function updated(ServiceRecord $service): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::SERVICE_RECORD_UPDATED->value])->each(function (Webhook $webhook) use ($service): void {
            WebhookService::dispatch($webhook, WebhookEvent::SERVICE_RECORD_UPDATED->value, $service);
        });

        $this->dispatchAutomationUpdated($service, AutomationTrigger::SERVICE_RECORD_UPDATED);
    }

    public function deleted(ServiceRecord $service): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::SERVICE_RECORD_DELETED->value])->each(function (Webhook $webhook) use ($service): void {
            WebhookService::dispatch($webhook, WebhookEvent::SERVICE_RECORD_DELETED->value, $service);
        });

        $this->dispatchAutomationDeleted($service, AutomationTrigger::SERVICE_RECORD_DELETED);
    }
}
