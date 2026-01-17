<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Webhook;
use App\Models\WebhookLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Context;
use Spatie\Activitylog\ActivityLogger;
use Spatie\WebhookServer\WebhookCall;

class WebhookService
{
    public static function dispatch(Webhook $webhook, string $event, mixed $data): PendingDispatch
    {
        $payload = [
            'event' => $event,
            'created' => now(),
            'request_id' => Context::get('request_id'),
            'trace_id' => Context::get('trace_id'),
        ];

        if (is_array($data)) {
            data_set($payload, 'data', $data);
            data_set($payload, 'changes', null);
        } elseif ($data instanceof Model) {
            data_set($payload, 'data', $data->toArray());
            data_set($payload, 'changes', $data->getChanges());
        }

        /** @var WebhookLog $log */
        $log = activity('webhook')
            ->withProperties([
                'payload' => $payload,
            ])
            ->when($data instanceof Model, fn (ActivityLogger $activity): ActivityLogger => $activity->causedBy($data))
            ->when(is_array($data), fn (ActivityLogger $activity): ActivityLogger => $activity->causedBy(Auth::user()))
            ->performedOn($webhook)
            ->log($webhook->url);

        return WebhookCall::create()
            ->meta([
                'model_id' => $log->getKey(),
            ])
            ->url($webhook->url)
            ->useHttpVerb($webhook->method->value)
            ->useSecret($webhook->secret)
            ->payload($payload)
            ->dispatch();
    }
}
