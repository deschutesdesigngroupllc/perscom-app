<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Support\Passport;

use App\Models\PassportClient;
use App\Models\User;
use Laravel\Passport\ClientRepository;
use Tests\Feature\Tenant\TenantTestCase;

class ClientTest extends TenantTestCase
{
    public function test_confidential_client_can_be_created(): void
    {
        /** @var ClientRepository $clients */
        $clients = resolve(ClientRepository::class);

        $user = User::factory()->createQuietly();

        $client = $clients->createAuthorizationCodeGrantClient(
            name: $this->faker->word,
            redirectUris: [$this->faker->url],
            user: $user,
        );

        $this->assertNotNull($client->plainSecret);
        $this->assertInstanceOf(PassportClient::class, $client);
    }

    public function test_non_confidential_client_can_be_created(): void
    {
        /** @var ClientRepository $clients */
        $clients = resolve(ClientRepository::class);

        $user = User::factory()->createQuietly();

        $client = $clients->createAuthorizationCodeGrantClient(
            name: $this->faker->word,
            redirectUris: [$this->faker->url],
            confidential: false,
            user: $user,
        );

        $this->assertNull($client->plainSecret);
        $this->assertInstanceOf(PassportClient::class, $client);
    }

    public function test_password_client_can_be_created(): void
    {
        /** @var ClientRepository $clients */
        $clients = resolve(ClientRepository::class);

        $client = $clients->createPasswordGrantClient(
            name: $this->faker->word,
        );

        $this->assertTrue($client->firstParty());
        $this->assertInstanceOf(PassportClient::class, $client);
    }

    public function test_personal_access_client_can_be_created(): void
    {
        /** @var ClientRepository $clients */
        $clients = resolve(ClientRepository::class);

        $client = $clients->createPersonalAccessGrantClient(
            name: $this->faker->word,
        );

        $this->assertTrue($client->firstParty());
        $this->assertInstanceOf(PassportClient::class, $client);
    }
}
