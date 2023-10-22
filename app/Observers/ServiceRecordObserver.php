<?php

namespace App\Observers;

use App\Models\Enums\WebhookEvent;
use App\Models\ServiceRecord;
use App\Models\Webhook;
use App\Notifications\Tenant\NewServiceRecord;
use App\Services\WebhookService;
use Illuminate\Support\Facades\Notification;

class ServiceRecordObserver
{
    public function created(ServiceRecord $service): void
    {
        Notification::send($service->user, new NewServiceRecord($service));

        Webhook::query()->whereJsonContains('events', [WebhookEvent::SERVICE_RECORD_CREATED->value])->each(function (Webhook $webhook) use ($service) {
            WebhookService::dispatch($webhook, WebhookEvent::SERVICE_RECORD_CREATED->value, $service);
        });
    }

    public function updated(ServiceRecord $service): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::SERVICE_RECORD_UPDATED->value])->each(function (Webhook $webhook) use ($service) {
            WebhookService::dispatch($webhook, WebhookEvent::SERVICE_RECORD_UPDATED->value, $service);
        });
    }

    public function deleted(ServiceRecord $service): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::SERVICE_RECORD_DELETED->value])->each(function (Webhook $webhook) use ($service) {
            WebhookService::dispatch($webhook, WebhookEvent::SERVICE_RECORD_DELETED->value, $service);
        });
    }
}
