<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Exceptions;

use App\Http\Middleware\CheckSubscription;
use Illuminate\Support\Facades\Route;
use Tests\Feature\Tenant\TenantTestCase;

class ErrorApiTest extends TenantTestCase
{
    public function test_4xx_exception_is_thrown_and_error_is_returned(): void
    {
        Route::get('/test-route', static function (): void {
            abort(499, 'foo bar');
        })->name('api.test');

        $this->getJson('/test-route')
            ->assertJsonPath('error.message', 'foo bar')
            ->assertJsonPath('error.type', 'HttpException')
            ->assertStatus(499);
    }

    public function test_401_exception_is_thrown_and_error_is_returned(): void
    {
        Route::get('/test-route', static fn () => response()->json('test'))->name('api.test')->middleware('auth:api');

        $this->getJson('/test-route')
            ->assertJsonPath('error.message', 'You are not authenticated. Please provide a valid API key that contains your PERSCOM ID to continue.')
            ->assertJsonPath('error.type', 'AuthenticationException')
            ->assertStatus(401);
    }

    public function test_402_exception_is_thrown_and_error_is_returned(): void
    {
        Route::get('/test-route', static function (): void {
            abort(402);
        })->name('api.test')->middleware(CheckSubscription::class);

        $this->getJson('/test-route')
            ->assertJsonPath('error.message', 'A valid subscription is required to complete this request.')
            ->assertJsonPath('error.type', 'HttpException')
            ->assertStatus(402);
    }

    public function test_403_exception_is_thrown_and_error_is_returned(): void
    {
        Route::get('/test-route', static function (): void {
            abort(403);
        })->name('api.test');

        $this->getJson('/test-route')
            ->assertJsonPath('error.message', 'The API key provided does not have the correct permissions and/or scopes to perform the requested action.')
            ->assertJsonPath('error.type', 'HttpException')
            ->assertStatus(403);
    }

    public function test_404_exception_is_thrown_and_error_is_returned(): void
    {
        $this->getJson('/test-route')
            ->assertJsonPath('error.message', 'The requested resource could not be found.')
            ->assertJsonPath('error.type', 'NotFoundHttpException')
            ->assertStatus(404);
    }

    public function test_419_exception_is_thrown_and_error_is_returned(): void
    {
        Route::get('/test-route', static function (): void {
            abort(419, 'foo bar');
        })->name('api.test');

        $this->getJson('/test-route')
            ->assertJsonPath('error.message', 'foo bar')
            ->assertJsonPath('error.type', 'HttpException')
            ->assertStatus(419);
    }

    public function test_429_exception_is_thrown_and_error_is_returned(): void
    {
        Route::get('/test-route', static function (): void {
            abort(429);
        })->name('api.test');

        $this->getJson('/test-route')
            ->assertJsonPath('error.message', 'You have exceeded the API rate limit. Please wait a minute before trying again.')
            ->assertJsonPath('error.type', 'HttpException')
            ->assertStatus(429);
    }

    public function test_5xx_exception_is_thrown_and_error_is_returned(): void
    {
        Route::get('/test-route', static function (): void {
            abort(599, 'foo bar');
        })->name('api.test');

        $this->getJson('/test-route')
            ->assertJsonPath('error.message', 'foo bar')
            ->assertJsonPath('error.type', 'HttpException')
            ->assertStatus(599);
    }

    public function test_500_exception_is_thrown_and_error_is_returned(): void
    {
        Route::get('/test-route', static function (): void {
            abort(500);
        })->name('api.test');

        $this->getJson('/test-route')
            ->assertJsonPath('error.message', 'There was a server error with your last request. Please try again.')
            ->assertJsonPath('error.type', 'HttpException')
            ->assertStatus(500);
    }
}
