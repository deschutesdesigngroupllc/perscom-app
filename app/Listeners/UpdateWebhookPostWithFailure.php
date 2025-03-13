<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\WebhookLog;
use GuzzleHttp\Psr7\Response;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\WebhookServer\Events\FinalWebhookCallFailedEvent;

class UpdateWebhookPostWithFailure implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(FinalWebhookCallFailedEvent $event): void
    {
        $modelId = data_get($event->meta, 'model_id');

        /** @var WebhookLog $webhookLog */
        $webhookLog = WebhookLog::find($modelId);

        if (filled($webhookLog) && filled($modelId)) {
            $properties = $webhookLog->properties;
            $properties->add([
                'error_type' => $event->errorType,
                'error_message' => $event->errorMessage,
            ]);

            $webhookLog->properties = $properties;
            $webhookLog->save();

            if ($event->response instanceof Response) {
                $properties = $webhookLog->properties;
                $properties->add([
                    'status_code' => $event->response->getStatusCode(),
                    'reason_phrase' => $event->response->getReasonPhrase(),
                ]);

                $webhookLog->properties = $properties;
                $webhookLog->save();
            }
        }
    }
}
