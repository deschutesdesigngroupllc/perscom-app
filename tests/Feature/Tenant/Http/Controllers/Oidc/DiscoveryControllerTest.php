<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Oidc;

use App\Http\Middleware\CheckSubscription;
use Tests\Feature\Tenant\TenantTestCase;

class DiscoveryControllerTest extends TenantTestCase
{
    public function test_discovery_page_can_be_reached()
    {
        $this->withoutMiddleware(CheckSubscription::class);

        $this->get(route('oidc.discovery'))
            ->assertSuccessful()
            ->assertExactJson([
                'issuer' => $this->tenant->url,
                'authorization_endpoint' => route('passport.authorizations.authorize'),
                'token_endpoint' => route('passport.token'),
                'userinfo_endpoint' => route('oidc.userinfo'),
                'grant_types_supported' => [
                    'authorization_code',
                    'implicit',
                    'refresh_token',
                    'client_credentials',
                    'password',
                ],
                'subject_types_supported' => [
                    'public',
                ],
                'response_types_supported' => [
                    'code',
                    'token',
                ],
                'response_modes_supported' => [
                    'query',
                ],
                'id_token_signing_alg_values_supported' => [
                    'HS256',
                ],
                'token_endpoint_auth_signing_alg_values_supported' => [
                    'RS256',
                ],
                'scopes_supported' => [
                    'openid',
                    'profile',
                    'email',
                    'tenant',
                ],
                'token_endpoint_auth_methods_supported' => [
                    'client_secret_basic',
                    'client_secret_post',
                ],
                'claims_supported' => [
                    'aud',
                    'iss',
                    'iat',
                    'exp',
                    'sub',
                    'name',
                    'preferred_username',
                    'profile',
                    'email',
                    'email_verified',
                    'picture',
                    'tenant',
                    'locale',
                    'zonefinfo',
                    'updated_at',
                ],
                'end_session_endpoint' => route('oidc.logout'),
            ]);
    }
}
