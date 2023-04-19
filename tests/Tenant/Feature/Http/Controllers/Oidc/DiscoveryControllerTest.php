<?php

namespace Tests\Tenant\Feature\Http\Controllers\Oidc;

use App\Http\Middleware\Subscribed;
use Tests\Tenant\TenantTestCase;

class DiscoveryControllerTest extends TenantTestCase
{
    public function test_discovey_page_can_be_reached()
    {
        $this->withoutMiddleware(Subscribed::class);

        $this->get($this->tenant->url.'/.well-known/openid-configuration')
            ->assertSuccessful()
            ->assertExactJson([
                'issuer' => $this->tenant->url,
                'authorization_endpoint' => $this->tenant->url.'/oauth/authorize',
                'token_endpoint' => $this->tenant->url.'/oauth/token',
                'userinfo_endpoint' => $this->tenant->url.'/oauth/userinfo',
                'grant_types_supported' => [
                    'authorization_code',
                    'implicit',
                    'refresh_token',
                ],
                'subject_types_supported' => [
                    'public',
                ],
                'id_token_signing_alg_values_supported' => [
                    'RS256',
                ],
                'scopes_supported' => [
                    'openid',
                    'profile',
                    'email',
                ],
                'token_endpoint_auth_methods_supported' => [
                    'client_secret_basic',
                    'client_secret_post',
                ],
                'claims_supported' => [
                    'jti',
                    'iat',
                    'nbf',
                    'exp',
                    'sub',
                    'scopes',
                ],
                'end_session_endpoint' => $this->tenant->url.'/oauth/logout',
            ]);
    }
}
