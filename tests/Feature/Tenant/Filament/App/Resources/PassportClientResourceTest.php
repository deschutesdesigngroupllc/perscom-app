<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Resources;

use App\Filament\App\Resources\PassportClientResource\Pages\CreatePassportClient;
use App\Filament\App\Resources\PassportClientResource\Pages\ViewPassportClient;
use App\Models\Enums\PassportClientType;
use App\Models\PassportClient;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\ClientRepository;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class PassportClientResourceTest extends TenantTestCase
{
    public function test_creating_a_client_hands_the_plain_secret_to_the_view_page_once(): void
    {
        $this->actingAsSuperAdmin();

        Livewire::test(CreatePassportClient::class)
            ->fillForm([
                'name' => 'Postman',
                'type' => PassportClientType::CLIENT_CREDENTIALS,
                'all_scopes' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $client = PassportClient::query()->where('name', 'Postman')->firstOrFail();
        $secret = session(ViewPassportClient::PLAIN_SECRET_SESSION_KEY);

        $this->assertIsString($secret);
        $this->assertNotSame($secret, $client->secret);
        $this->assertTrue(Hash::check($secret, $client->secret));

        Livewire::test(ViewPassportClient::class, ['record' => $client->getKey()])
            ->assertSet('defaultAction', 'showSecret')
            ->assertSet('plainSecret', $secret)
            ->mountAction('showSecret')
            ->assertActionMounted('showSecret')
            ->assertSchemaStateSet(['secret' => $secret]);

        $this->assertNull(session(ViewPassportClient::PLAIN_SECRET_SESSION_KEY));
    }

    public function test_the_view_page_does_not_show_a_secret_without_one_to_show(): void
    {
        $this->actingAsSuperAdmin();

        $client = $this->client();

        Livewire::test(ViewPassportClient::class, ['record' => $client->getKey()])
            ->assertSet('defaultAction', null)
            ->assertSet('plainSecret', null)
            ->assertDontSee($client->secret);
    }

    public function test_regenerating_the_secret_shows_the_new_plain_secret(): void
    {
        $this->actingAsSuperAdmin();

        $client = $this->client();
        $oldHash = $client->secret;

        $component = Livewire::test(ViewPassportClient::class, ['record' => $client->getKey()])
            ->callAction('regenerateSecret')
            ->assertActionMounted('showSecret');

        $secret = $component->get('plainSecret');
        $client->refresh();

        $this->assertIsString($secret);
        $this->assertNotSame($oldHash, $client->secret);
        $this->assertTrue(Hash::check($secret, $client->secret));
    }

    public function test_the_endpoints_tab_lists_urls_without_a_tenant_query_parameter(): void
    {
        $this->actingAsSuperAdmin();

        Livewire::test(ViewPassportClient::class, ['record' => $this->client()->getKey()])
            ->assertSee($this->tenant->url.'/.well-known/openid-configuration')
            ->assertSee($this->tenant->url.'/oauth/authorize')
            ->assertSee($this->tenant->url.'/oauth/userinfo')
            ->assertDontSee('?tenant=');
    }

    private function actingAsSuperAdmin(): User
    {
        $user = User::factory()->createQuietly();
        $user->assignRole(Utils::getSuperAdminName());

        $this->actingAs($user);

        return $user;
    }

    private function client(): PassportClient
    {
        /** @var PassportClient $client */
        $client = resolve(ClientRepository::class)->createClientCredentialsGrantClient('Existing Client');

        return $client;
    }
}
