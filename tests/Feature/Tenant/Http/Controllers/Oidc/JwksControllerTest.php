<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Oidc;

use App\Support\Passport\JsonWebKey;
use Firebase\JWT\JWK;
use Tests\Feature\Tenant\TenantTestCase;

class JwksControllerTest extends TenantTestCase
{
    public function test_it_returns_the_public_signing_key(): void
    {
        $response = $this->get($this->tenant->route('oidc.jwks'))
            ->assertSuccessful()
            ->assertJsonCount(1, 'keys')
            ->assertJsonPath('keys.0.kty', 'RSA')
            ->assertJsonPath('keys.0.use', 'sig')
            ->assertJsonPath('keys.0.alg', 'RS256')
            ->assertJsonPath('keys.0.kid', JsonWebKey::thumbprint())
            ->assertJsonStructure([
                'keys' => [
                    ['kty', 'use', 'alg', 'kid', 'n', 'e'],
                ],
            ]);

        $this->assertArrayHasKey(JsonWebKey::thumbprint(), JWK::parseKeySet($response->json()));
    }

    public function test_the_key_matches_the_passport_public_key(): void
    {
        $details = openssl_pkey_get_details(openssl_pkey_get_public((string) file_get_contents(storage_path('oauth-public.key'))));

        $this->assertSame(
            rtrim(strtr(base64_encode($details['rsa']['n']), '+/', '-_'), '='),
            JsonWebKey::publicKey()['n'],
        );
    }
}
