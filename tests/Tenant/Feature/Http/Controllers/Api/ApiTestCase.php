<?php

namespace Tests\Tenant\Feature\Http\Controllers\Api;

use App\Http\Middleware\LogApiRequests;
use App\Http\Middleware\SentryContext;
use App\Http\Middleware\Subscribed;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\URL;
use Tests\Tenant\TenantTestCase;
use Treblle\Middlewares\TreblleMiddleware;

class ApiTestCase extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        URL::forceRootUrl(config('app.api_url').'/'.config('app.api_version'));

        $this->withHeader('X-Perscom-Id', $this->tenant->getTenantKey());

        $this->withoutMiddleware([
            TreblleMiddleware::class,
            SentryContext::class,
            LogApiRequests::class,
            ThrottleRequests::class,
            Subscribed::class,
        ]);
    }
}
