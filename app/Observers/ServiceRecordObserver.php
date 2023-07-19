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
    /**
     * Handle the Service "created" event.
     *
     * @return void
     */
    public function created(ServiceRecord $service)
    {
        Notification::send($service->user, new NewServiceRecord($service));

        Webhook::query()->whereJsonContains('events', [WebhookEvent::SERVICE_RECORD_CREATED->value])->each(function (Webhook $webhook) use ($service) {
            WebhookService::dispatch($webhook, WebhookEvent::SERVICE_RECORD_CREATED->value, $service);
        });
    }

    /**
     * Handle the Service "updated" event.
     *
     * @return void
     */
    public function updated(ServiceRecord $service)
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::SERVICE_RECORD_UPDATED->value])->each(function (Webhook $webhook) use ($service) {
            WebhookService::dispatch($webhook, WebhookEvent::SERVICE_RECORD_UPDATED->value, $service);
        });
    }

    /**
     * Handle the Service "deleted" event.
     *
     * @return void
     */
    public function deleted(ServiceRecord $service)
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::SERVICE_RECORD_DELETED->value])->each(function (Webhook $webhook) use ($service) {
            WebhookService::dispatch($webhook, WebhookEvent::SERVICE_RECORD_DELETED->value, $service);
        });
    }
}
