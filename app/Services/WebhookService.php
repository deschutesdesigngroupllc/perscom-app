<?php

namespace App\Services;

use App\Models\Webhook;
use Spatie\WebhookServer\WebhookCall;

class WebhookService
{
    /**
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    public static function dispatch(Webhook $webhook, $event, $model)
    {
        $payload = [
            'event' => $event,
            'created' => now(),
            'data' => $model->toArray(),
            'changes' => $model->getChanges(),
        ];

        activity('webhook')
            ->withProperties($payload)
            ->causedBy($model)
            ->performedOn($webhook)
            ->log($webhook->url);

        return WebhookCall::create()
            ->url($webhook->url)
            ->useHttpVerb($webhook->method->value)
            ->useSecret($webhook->secret)
            ->payload($payload)
            ->dispatch();
    }
}
