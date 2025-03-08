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
        $clients = app(ClientRepository::class);

        $user = User::factory()->createQuietly();

        $client = $clients->create(
            userId: $user->getKey(),
            name: $this->faker->word,
            redirect: $this->faker->url,
        );

        $this->assertFalse($client->firstParty());
        $this->assertEquals($client->user->getKey(), $user->getKey());
        $this->assertNotNull($client->plainSecret);
        $this->assertInstanceOf(PassportClient::class, $client);
    }

    public function test_non_confidential_client_can_be_created(): void
    {
        /** @var ClientRepository $clients */
        $clients = app(ClientRepository::class);

        $user = User::factory()->createQuietly();

        $client = $clients->create(
            userId: $user->getKey(),
            name: $this->faker->word,
            redirect: $this->faker->url,
            confidential: false
        );

        $this->assertFalse($client->firstParty());
        $this->assertEquals($client->user->getKey(), $user->getKey());
        $this->assertNull($client->plainSecret);
        $this->assertInstanceOf(PassportClient::class, $client);
    }

    public function test_password_client_can_be_created(): void
    {
        /** @var ClientRepository $clients */
        $clients = app(ClientRepository::class);

        $user = User::factory()->createQuietly();

        $client = $clients->createPasswordGrantClient(
            userId: $user->getKey(),
            name: $this->faker->word,
            redirect: $this->faker->url,
            provider: 'users'
        );

        $this->assertTrue($client->firstParty());
        $this->assertEquals($client->user->getKey(), $user->getKey());
        $this->assertInstanceOf(PassportClient::class, $client);
    }

    public function test_personal_access_client_can_be_created(): void
    {
        /** @var ClientRepository $clients */
        $clients = app(ClientRepository::class);

        $user = User::factory()->createQuietly();

        $client = $clients->createPersonalAccessClient(
            userId: $user->getKey(),
            name: $this->faker->word,
            redirect: $this->faker->url,
        );

        $this->assertTrue($client->firstParty());
        $this->assertEquals($client->user->getKey(), $user->getKey());
        $this->assertInstanceOf(PassportClient::class, $client);
    }
}
