<?php

declare(strict_types=1);

namespace Tests;

use App\Features\BaseFeature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Routing\Middleware\ThrottleRequestsWithRedis;
use Illuminate\Support\Facades\Http;
use Spatie\ResponseCache\Middlewares\CacheResponse;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();
        BaseFeature::resetTenant();

        $this->withoutMiddleware([ThrottleRequestsWithRedis::class, CacheResponse::class]);
    }
}
