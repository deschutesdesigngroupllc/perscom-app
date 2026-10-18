<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Middleware;

use App\Http\Middleware\CaptureUserOnlineStatus;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\Tenant\TenantTestCase;

class CaptureUserOnlineStatusTest extends TenantTestCase
{
    public function test_it_records_last_seen_without_touching_timestamps_or_leaving_them_disabled(): void
    {
        $user = User::factory()->createQuietly(['updated_at' => now()->subDay()]);
        $updatedAt = $user->updated_at;

        $this->actingAs($user);

        (new CaptureUserOnlineStatus)->handle(Request::create('/'), fn (Request $request): Response => new Response('ok'));

        /** @var User $authenticated */
        $authenticated = Auth::user();

        $this->assertTrue($authenticated->usesTimestamps());
        $this->assertInstanceOf(CarbonInterface::class, $authenticated->created_at);
        $this->assertNotNull($authenticated->fresh()->last_seen_at);
        $this->assertTrue($updatedAt->equalTo($authenticated->fresh()->updated_at));
    }
}
