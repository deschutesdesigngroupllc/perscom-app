<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Middleware\InitializeTenancyByRequestData;
use App\Http\Middleware\LogApiRequests;
use App\Http\Middleware\SentryContext;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ApiTestCase extends TestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        URL::forceRootUrl(config('app.api_url').'/'.config('app.api_version'));

        $this->withoutMiddleware([
            InitializeTenancyByRequestData::class,
            'treblle',
            SentryContext::class,
            LogApiRequests::class,
            ThrottleRequests::class,
            'subscribed',
        ]);
    }
}
