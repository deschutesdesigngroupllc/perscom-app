<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Middleware;

use App\Http\Middleware\CaptureOidcAuthorization;
use App\Models\User;
use App\Support\Passport\IdTokenResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\Tenant\TenantTestCase;

class CaptureOidcAuthorizationTest extends TenantTestCase
{
    public function test_it_remembers_the_nonce_of_an_openid_authorization_request(): void
    {
        $user = User::factory()->createQuietly();

        Cache::spy();

        $request = Request::create('/oauth/authorize', parameters: [
            'client_id' => 'client-id',
            'scope' => 'openid profile',
            'nonce' => 'nonce-value',
        ]);
        $request->setUserResolver(fn (): User => $user);

        (new CaptureOidcAuthorization)->handle($request, fn (): Response => new Response);

        Cache::shouldHaveReceived('put')
            ->once()
            ->withArgs(fn (string $key, array $value): bool => $key === IdTokenResponse::authorizationCacheKey('client-id', (string) $user->getKey())
                && $value['nonce'] === 'nonce-value'
                && is_int($value['auth_time']));
    }

    public function test_it_ignores_requests_without_the_openid_scope(): void
    {
        $user = User::factory()->createQuietly();

        Cache::spy();

        $request = Request::create('/oauth/authorize', parameters: [
            'client_id' => 'client-id',
            'scope' => 'view:user',
            'nonce' => 'nonce-value',
        ]);
        $request->setUserResolver(fn (): User => $user);

        (new CaptureOidcAuthorization)->handle($request, fn (): Response => new Response);

        Cache::shouldNotHaveReceived('put');
    }

    public function test_it_ignores_guests(): void
    {
        Cache::spy();

        $request = Request::create('/oauth/authorize', parameters: [
            'client_id' => 'client-id',
            'scope' => 'openid',
            'nonce' => 'nonce-value',
        ]);

        (new CaptureOidcAuthorization)->handle($request, fn (): Response => new Response);

        Cache::shouldNotHaveReceived('put');
    }
}
