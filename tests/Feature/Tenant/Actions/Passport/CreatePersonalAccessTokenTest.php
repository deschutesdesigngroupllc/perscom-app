<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Passport;

use App\Actions\Passport\CreatePersonalAccessToken;
use App\Models\User;
use Laravel\Passport\PersonalAccessTokenResult;
use Tests\Feature\Tenant\TenantTestCase;

class CreatePersonalAccessTokenTest extends TenantTestCase
{
    public function test_it_creates_a_personal_access_token_for_the_user(): void
    {
        $user = User::factory()->createQuietly();

        $result = CreatePersonalAccessToken::handle($user, 'API Token', ['view:user']);

        $this->assertInstanceOf(PersonalAccessTokenResult::class, $result);
        $this->assertNotEmpty($result->accessToken);
        $this->assertSame('API Token', $result->getToken()->getAttribute('name'));
    }

    public function test_it_persists_the_plain_access_token_on_the_token_model(): void
    {
        $user = User::factory()->createQuietly();

        $result = CreatePersonalAccessToken::handle($user, 'Persisted Token', ['view:user']);

        $token = $result->getToken()->fresh();

        $this->assertSame($result->accessToken, $token->getAttribute('token'));
    }
}
