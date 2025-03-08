<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Oidc;

use Tests\Feature\Tenant\TenantTestCase;

class DiscoveryControllerTest extends TenantTestCase
{
    public function test_it_can_return_the_discovery_endpoint(): void
    {
        $this->get($this->tenant->route('oidc.discovery'))
            ->assertSuccessful()
            ->assertExactJson([
                'issuer' => $this->tenant->url,
                'authorization_endpoint' => $this->tenant->route('passport.authorizations.authorize'),
                'token_endpoint' => $this->tenant->route('passport.token'),
                'userinfo_endpoint' => $this->tenant->route('oidc.userinfo'),
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
                    'fragment',
                ],
                'id_token_signing_alg_values_supported' => [
                    'HS256',
                ],
                'scopes_supported' => [
                    'openid',
                    'profile',
                    'email',
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
                    'phone_number',
                    'tenant',
                    'roles',
                    'locale',
                    'zoneinfo',
                    'updated_at',
                ],
                'end_session_endpoint' => $this->tenant->route('oidc.logout'),
            ]);
    }
}
