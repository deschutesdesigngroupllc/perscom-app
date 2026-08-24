<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Middleware;

use App\Http\Middleware\CaptureUserOnlineStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Mechanisms\PersistentMiddleware\PersistentMiddleware;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CaptureUserOnlineStatusTest extends TestCase
{
    public function test_it_is_registered_as_persistent_middleware_so_livewire_reruns_it_after_tenancy(): void
    {
        $this->assertContains(
            CaptureUserOnlineStatus::class,
            resolve(PersistentMiddleware::class)->getPersistentMiddleware(),
        );
    }

    public function test_it_is_not_in_the_global_web_group_where_it_would_run_before_tenancy(): void
    {
        $this->assertNotContains(
            CaptureUserOnlineStatus::class,
            Route::getMiddlewareGroups()['web'] ?? [],
        );
    }

    public function test_it_resolves_the_web_guard_and_passes_the_request_through(): void
    {
        Auth::shouldReceive('guard')->with('web')->andReturnSelf();
        Auth::shouldReceive('check')->once()->andReturnFalse();

        $response = (new CaptureUserOnlineStatus)->handle(
            Request::create('/'),
            fn (Request $request): Response => new Response('ok'),
        );

        $this->assertSame('ok', $response->getContent());
    }
}
