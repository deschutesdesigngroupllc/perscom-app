<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Middleware;

use App\Http\Middleware\CaptureOidcAuthorization;
use App\Support\Passport\OidcAuthorization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\Tenant\TenantTestCase;

class CaptureOidcAuthorizationTest extends TenantTestCase
{
    public function test_it_exposes_the_nonce_to_an_authorization_code_issued_within_the_request(): void
    {
        $request = Request::create('/oauth/authorize', parameters: [
            'client_id' => 'client-id',
            'scope' => 'openid profile',
            'nonce' => 'nonce-value',
        ]);

        $this->app->instance('request', $request);

        $nonce = null;

        (new CaptureOidcAuthorization)->handle($request, function () use (&$nonce): Response {
            $nonce = OidcAuthorization::nonce();

            return new Response;
        });

        $this->assertSame('nonce-value', $nonce);
        $this->assertNull(Session::get(OidcAuthorization::PENDING_SESSION_KEY));
    }

    public function test_it_binds_the_nonce_to_the_authorization_request_awaiting_consent(): void
    {
        $request = Request::create('/oauth/authorize', parameters: [
            'client_id' => 'client-id',
            'scope' => 'openid',
            'nonce' => 'nonce-value',
        ]);

        (new CaptureOidcAuthorization)->handle($request, function (): Response {
            Session::put('authToken', 'auth-token');

            return new Response;
        });

        $this->assertSame([
            'auth_token' => 'auth-token',
            'nonce' => 'nonce-value',
        ], Session::get(OidcAuthorization::PENDING_SESSION_KEY));
    }

    public function test_it_does_not_rebind_a_previous_authorization_request_awaiting_consent(): void
    {
        Session::put('authToken', 'previous-auth-token');
        Session::put(OidcAuthorization::PENDING_SESSION_KEY, [
            'auth_token' => 'previous-auth-token',
            'nonce' => 'previous-nonce',
        ]);

        $request = Request::create('/oauth/authorize', parameters: [
            'client_id' => 'client-id',
            'scope' => 'openid',
            'nonce' => 'nonce-value',
        ]);

        (new CaptureOidcAuthorization)->handle($request, fn (): Response => new Response);

        $this->assertSame('previous-nonce', Session::get(OidcAuthorization::PENDING_SESSION_KEY)['nonce']);
    }

    public function test_it_ignores_requests_without_the_openid_scope(): void
    {
        $request = Request::create('/oauth/authorize', parameters: [
            'client_id' => 'client-id',
            'scope' => 'view:user',
            'nonce' => 'nonce-value',
        ]);

        (new CaptureOidcAuthorization)->handle($request, function () use ($request): Response {
            Session::put('authToken', 'auth-token');

            $this->assertFalse($request->attributes->has(OidcAuthorization::REQUEST_ATTRIBUTE));

            return new Response;
        });

        $this->assertNull(Session::get(OidcAuthorization::PENDING_SESSION_KEY));
    }
}
