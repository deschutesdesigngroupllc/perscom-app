<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Exceptions;

use App\Http\Middleware\CheckSubscription;
use Illuminate\Support\Facades\Route;
use Tests\Feature\Central\CentralTestCase;

class ErrorApiTest extends CentralTestCase
{
    public function test_4xx_exception_is_thrown_and_error_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(499, 'foo bar');
        })->name('api.test');

        $this->getJson('/test-route')
            ->assertJsonPath('error.message', 'foo bar')
            ->assertJsonPath('error.type', 'HttpException')
            ->assertStatus(499);
    }

    public function test_401_exception_is_thrown_and_error_is_returned()
    {
        Route::get('/test-route', static function () {
            return response()->json('test');
        })->name('api.test')->middleware('auth:api');

        $this->getJson('/test-route')
            ->assertJsonPath('error.message', 'Unauthenticated.')
            ->assertJsonPath('error.type', 'AuthenticationException')
            ->assertStatus(401);
    }

    public function test_402_exception_is_thrown_and_error_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(402, 'foo bar.');
        })->name('api.test')->middleware(CheckSubscription::class);

        $this->getJson('/test-route')
            ->assertJsonPath('error.message', 'A valid subscription is required to make an API request.')
            ->assertJsonPath('error.type', 'HttpException')
            ->assertStatus(402);
    }

    public function test_403_exception_is_thrown_and_error_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(403, 'foo bar');
        })->name('api.test');

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
        })->name('api.test');

        $this->getJson('/test-route')
            ->assertJsonPath('error.message', 'foo bar')
            ->assertJsonPath('error.type', 'HttpException')
            ->assertStatus(419);
    }

    public function test_429_exception_is_thrown_and_error_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(429, 'foo bar');
        })->name('api.test');

        $this->getJson('/test-route')
            ->assertJsonPath('error.message', 'foo bar')
            ->assertJsonPath('error.type', 'HttpException')
            ->assertStatus(429);
    }

    public function test_5xx_exception_is_thrown_and_error_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(599, 'foo bar');
        })->name('api.test');

        $this->getJson('/test-route')
            ->assertJsonPath('error.message', 'foo bar')
            ->assertJsonPath('error.type', 'HttpException')
            ->assertStatus(599);
    }

    public function test_500_exception_is_thrown_and_error_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(500, 'foo bar');
        })->name('api.test');

        $this->getJson('/test-route')
            ->assertJsonPath('error.message', 'foo bar')
            ->assertJsonPath('error.type', 'HttpException')
            ->assertStatus(500);
    }
}
