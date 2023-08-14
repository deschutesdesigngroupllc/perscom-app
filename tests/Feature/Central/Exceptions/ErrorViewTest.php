<?php

namespace Tests\Feature\Central\Exceptions;

use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia;
use Tests\Feature\Central\CentralTestCase;

class ErrorViewTest extends CentralTestCase
{
    public function test_4xx_exception_is_thrown_and_view_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(499, 'foo bar');
        });

        $this->get('/test-route')
            ->assertInertia(function (AssertableInertia $page) {
                $page->component('Error')->has('message');
            })->assertStatus(499);
    }

    public function test_401_exception_is_thrown_and_view_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(401);
        });

        $this->get('/test-route')
            ->assertInertia(function (AssertableInertia $page) {
                $page->component('Error')->has('message');
            })->assertStatus(401);
    }

    public function test_402_exception_is_thrown_and_view_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(402, 'foo bar');
        });

        $this->get('/test-route')
            ->assertInertia(function (AssertableInertia $page) {
                $page->component('Error')->has('message');
            })->assertStatus(402);
    }

    public function test_403_exception_is_thrown_and_view_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(403, 'foo bar');
        });

        $this->get('/test-route')
            ->assertInertia(function (AssertableInertia $page) {
                $page->component('Error')->has('message');
            })->assertStatus(403);
    }

    public function test_404_exception_is_thrown_and_view_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(404);
        });

        $this->get('/test-route')
            ->assertInertia(function (AssertableInertia $page) {
                $page->component('Error')->has('message');
            })->assertStatus(404);
    }

    public function test_419_exception_is_thrown_and_view_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(419);
        });

        $this->get('/test-route')
            ->assertInertia(function (AssertableInertia $page) {
                $page->component('Error')->has('message');
            })->assertStatus(419);
    }

    public function test_429_exception_is_thrown_and_view_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(429);
        });

        $this->get('/test-route')
            ->assertInertia(function (AssertableInertia $page) {
                $page->component('Error')->has('message');
            })->assertStatus(429);
    }

    public function test_5xx_exception_is_thrown_and_view_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(599, 'foo bar');
        });

        $this->get('/test-route')
            ->assertInertia(function (AssertableInertia $page) {
                $page->component('Error')->has('message');
            })->assertStatus(599);
    }

    public function test_500_exception_is_thrown_and_view_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(500);
        });

        $this->get('/test-route')
            ->assertInertia(function (AssertableInertia $page) {
                $page->component('Error')->has('message');
            })->assertStatus(500);
    }

    public function test_503_exception_is_thrown_and_view_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(503);
        });

        $this->get('/test-route')
            ->assertInertia(function (AssertableInertia $page) {
                $page->component('Error')->has('message');
            })->assertStatus(503);
    }
}
