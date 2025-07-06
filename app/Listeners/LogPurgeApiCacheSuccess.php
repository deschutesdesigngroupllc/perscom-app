<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ApiPurgedSuccessful;
use Illuminate\Support\Facades\Context;

class LogPurgeApiCacheSuccess
{
    public function handle(ApiPurgedSuccessful $event): void
    {
        if (! tenancy()->initialized) {
            return;
        }

        activity('api_purge')->withProperties([
            'tags' => $event->tags,
            'request_id' => Context::get('request_id'),
            'trace_id' => Context::get('trace_id'),
            'status' => 'success',
        ])->causedBy(request()->user())->log($event->event);
    }
}
