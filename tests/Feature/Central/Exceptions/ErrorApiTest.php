<?php

namespace Tests\Feature\Central\Exceptions;

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\Subscribed;
use Illuminate\Support\Facades\Route;
use Tests\Feature\Central\CentralTestCase;

class ErrorApiTest extends CentralTestCase
{
    public function test_4xx_exception_is_thrown_and_error_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(499, 'foo bar');
        });

        $this->getJson('/test-route')
            ->assertJsonPath('error.message', 'foo bar')
            ->assertJsonPath('error.type', 'HttpException')
            ->assertStatus(499);
    }

    public function test_401_exception_is_thrown_and_error_is_returned()
    {
        Route::get('/test-route', static function () {
            return response()->json('test');
        })->middleware(Authenticate::class);

        $this->getJson('/test-route')
            ->assertJsonPath('error.message', 'Unauthenticated.')
            ->assertJsonPath('error.type', 'AuthenticationException')
            ->assertStatus(401);
    }

    public function test_402_exception_is_thrown_and_error_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(402, 'Payment Required.');
        })->middleware(Subscribed::class);

        $this->getJson('/test-route')
            ->assertJsonPath('error.message', 'Payment Required.')
            ->assertJsonPath('error.type', 'HttpException')
            ->assertStatus(402);
    }

    public function test_403_exception_is_thrown_and_error_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(403, 'foo bar');
        });

        $this->getJson('/test-route')
            ->assertJsonPath('error.message', 'foo bar')
            ->assertJsonPath('error.type', 'HttpException')
            ->assertStatus(403);
    }

    public function test_404_exception_is_thrown_and_error_is_returned()
    {
        $this->getJson('/test-route')
            ->assertJsonPath('error.message', 'The route test-route could not be found.')
            ->assertJsonPath('error.type', 'NotFoundHttpException')
            ->assertStatus(404);
    }

    public function test_419_exception_is_thrown_and_error_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(419, 'foo bar');
        });

        $this->getJson('/test-route')
            ->assertJsonPath('error.message', 'foo bar')
            ->assertJsonPath('error.type', 'HttpException')
            ->assertStatus(419);
    }

    public function test_429_exception_is_thrown_and_error_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(429, 'foo bar');
        });

        $this->getJson('/test-route')
            ->assertJsonPath('error.message', 'foo bar')
            ->assertJsonPath('error.type', 'HttpException')
            ->assertStatus(429);
    }

    public function test_5xx_exception_is_thrown_and_error_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(599, 'foo bar');
        });

        $this->getJson('/test-route')
            ->assertJsonPath('error.message', 'foo bar')
            ->assertJsonPath('error.type', 'HttpException')
            ->assertStatus(599);
    }

    public function test_500_exception_is_thrown_and_error_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(500, 'foo bar');
        });

        $this->getJson('/test-route')
            ->assertJsonPath('error.message', 'foo bar')
            ->assertJsonPath('error.type', 'HttpException')
            ->assertStatus(500);
    }

    public function test_503_exception_is_thrown_and_error_is_returned()
    {
        $this->app->maintenanceMode()->activate([]);

        $this->getJson('/test-route')
            ->assertJsonPath('error.message', 'Service Unavailable')
            ->assertJsonPath('error.type', 'HttpException')
            ->assertStatus(503);

        $this->app->maintenanceMode()->deactivate();
    }
}
