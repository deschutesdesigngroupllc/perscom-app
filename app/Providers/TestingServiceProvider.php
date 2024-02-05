<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\ServiceProvider;
use Tests\TestCase;

class TestingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        ParallelTesting::setUpProcess(function (int $token) {
            Log::debug("Set up process $token");
        });

        ParallelTesting::setUpTestCase(function (int $token, TestCase $testCase) {
            Log::debug("Set up test case $token", [
                'case' => $testCase,
            ]);
        });

        ParallelTesting::setUpTestDatabase(function (string $database, int $token) {
            Log::debug('Set up test database', [
                'database' => $database,
            ]);
        });

        ParallelTesting::tearDownTestCase(function (int $token, TestCase $testCase) {
            Log::debug("Tear down test case $token", [
                'case' => $testCase,
            ]);
        });

        ParallelTesting::tearDownProcess(function (int $token) {
            Log::debug("Tear down test process $token");
        });
    }
}
