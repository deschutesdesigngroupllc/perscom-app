<?php

namespace Tests\Central\Unit\Exceptions;

use Illuminate\Support\Facades\Route;
use Tests\Central\CentralTestCase;

class ErrorViewTest extends CentralTestCase
{
    public function test_4xx_exception_is_thrown_and_view_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(499, 'foo bar');
        });

        $this->get('/test-route')
            ->assertStatus(499)
            ->assertSeeText('Bad Request.')
            ->assertSeeText('foo bar');
    }

    public function test_401_exception_is_thrown_and_view_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(401);
        });

        $this->get('/test-route')
            ->assertStatus(401)
            ->assertSeeText('Unauthorized.');
    }

    public function test_402_exception_is_thrown_and_view_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(402, 'foo bar');
        });

        $this->get('/test-route')
            ->assertStatus(402)
            ->assertSeeText('Subscription Required.')
            ->assertSeeText('foo bar');
    }

    public function test_403_exception_is_thrown_and_view_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(403, 'foo bar');
        });

        $this->get('/test-route')
            ->assertStatus(403)
            ->assertSeeText('Forbidden.')
            ->assertSeeText('foo bar');
    }

    public function test_404_exception_is_thrown_and_view_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(404);
        });

        $this->get('/test-route')
            ->assertStatus(404)
            ->assertSeeText('Page not found.');
    }

    public function test_419_exception_is_thrown_and_view_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(419);
        });

        $this->get('/test-route')
            ->assertStatus(419)
            ->assertSeeText('Page expired.');
    }

    public function test_429_exception_is_thrown_and_view_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(429);
        });

        $this->get('/test-route')
            ->assertStatus(429)
            ->assertSeeText('Too many requests.');
    }

    public function test_5xx_exception_is_thrown_and_view_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(599, 'foo bar');
        });

        $this->get('/test-route')
            ->assertStatus(599)
            ->assertSeeText('Server error.')
            ->assertSeeText('foo bar');
    }

    public function test_500_exception_is_thrown_and_view_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(500);
        });

        $this->get('/test-route')
            ->assertStatus(500)
            ->assertSeeText('Server error.');
    }

    public function test_503_exception_is_thrown_and_view_is_returned()
    {
        Route::get('/test-route', static function () {
            abort(503);
        });

        $this->get('/test-route')
            ->assertStatus(503)
            ->assertSeeText('Down for maintenance.');
    }
}
