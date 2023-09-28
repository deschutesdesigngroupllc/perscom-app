<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Routing\Middleware\ThrottleRequestsWithRedis;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\Str;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;
    use WithFaker;

    protected string $testToken;

    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();

        $this->withoutMiddleware(ThrottleRequestsWithRedis::class);

        $this->testToken = ParallelTesting::token() ?: Str::random();

        Log::debug('Running test', [
            'test' => $this->toString(),
            'token' => $this->getTestToken(),
        ]);
    }

    protected function getTestToken(): string
    {
        return $this->testToken;
    }
}
