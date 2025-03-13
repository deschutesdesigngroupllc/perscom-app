<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\WebhookLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\WebhookServer\Events\WebhookCallSucceededEvent;

class UpdateWebhookPostWithSuccess implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(WebhookCallSucceededEvent $event): void
    {
        $modelId = data_get($event->meta, 'model_id');

        /** @var WebhookLog $webhookLog */
        $webhookLog = WebhookLog::find($modelId);

        if (filled($webhookLog) && filled($modelId) && $event->response) {
            $properties = $webhookLog->properties;
            $properties->put('status_code', $event->response->getStatusCode());
            $properties->put('reason_phrase', $event->response->getReasonPhrase());

            $webhookLog->properties = $properties;
            $webhookLog->save();
        }
    }
}
