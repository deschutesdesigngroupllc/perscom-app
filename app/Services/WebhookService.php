<?php

namespace App\Services;

use App\Models\Webhook;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\PendingDispatch;
use Spatie\WebhookServer\WebhookCall;

class WebhookService
{
    public static function dispatch(Webhook $webhook, string $event, Model $model): PendingDispatch
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
