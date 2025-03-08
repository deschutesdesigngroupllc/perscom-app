<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Middleware;

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\Feature\Central\CentralTestCase;

class HandleInertiaRequestsTest extends CentralTestCase
{
    public function test_external_redirect_response_returns_409_status(): void
    {
        $url = $this->faker->url;

        Route::get('/redirect', static fn () => redirect($url))->middleware(HandleInertiaRequests::class);

        $this->withHeader('X-Inertia', 'true')
            ->get('/redirect')
            ->assertStatus(409);
    }

    public function test_internal_redirect_response_returns_302_status(): void
    {
        Route::post('/redirect', static fn () => to_route('web.landing.home'))->middleware(HandleInertiaRequests::class);

        $this->withHeader('X-Inertia', 'true')
            ->post('/redirect')
            ->assertRedirect();
    }

    public function test_internal_redirect_response_returns_303_status(): void
    {
        Route::delete('/redirect', static fn () => to_route('web.landing.home', status: 303))->middleware(HandleInertiaRequests::class);

        $this->withHeader('X-Inertia', 'true')
            ->delete('/redirect')
            ->assertRedirect();
    }

    public function test_internal_redirect_from_303_delete_returns_follow_up_with_get_request(): void
    {
        $this->followRedirects = true;

        Route::delete('/redirect', static fn () => to_route('web.landing.home', status: 303))->middleware(HandleInertiaRequests::class);

        $response = $this->withHeader('X-Inertia', 'true')
            ->delete('/redirect');

        $response->assertStatus(409);

        /** @var Request $baseRequest */
        $baseRequest = $response->baseResponse->baseRequest;

        $this->assertSame($baseRequest->getMethod(), 'GET');
    }
}
