<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ApiPurgedFailed;
use Illuminate\Support\Facades\Context;

class LogPurgeApiCacheFailure
{
    public function handle(ApiPurgedFailed $event): void
    {
        if (! tenancy()->initialized) {
            return;
        }

        activity('api_purge')->withProperties([
            'tags' => $event->tags,
            'request_id' => Context::get('request_id'),
            'trace_id' => Context::get('trace_id'),
            'status' => 'failure',
        ])->causedBy(request()->user())->log($event->event);
    }
}
