<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\App\Integrations;

use App\Models\Tenant;
use App\Models\User;
use App\Services\IntegrationInstallService;
use App\Settings\IntegrationSettings;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Tests\Feature\Tenant\TenantTestCase;

class DiscordConnectControllerTest extends TenantTestCase
{
    public function test_install_redirects_administrators_to_discord(): void
    {
        $this->actingAs($this->admin());

        $response = $this->get(route('app.integrations.install', ['provider' => 'discord']))->assertRedirect();

        $location = (string) $response->headers->get('Location');

        $this->assertStringStartsWith('https://discord.com/oauth2/authorize', $location);
        $this->assertStringContainsString('response_type=code', $location);
        $this->assertStringContainsString('applications.commands', urldecode($location));
        $this->assertNotNull(Session::get('integrations.discord.nonce'));
    }

    public function test_members_cannot_start_an_install(): void
    {
        $this->actingAs(User::factory()->createQuietly());

        $this->get(route('app.integrations.install', ['provider' => 'discord']))->assertForbidden();
    }

    public function test_the_central_callback_hands_off_to_the_organization(): void
    {
        $state = IntegrationInstallService::state('discord');

        $response = $this->get(route('integrations.install.callback', ['provider' => 'discord', 'state' => $state, 'code' => 'abc', 'guild_id' => 'guild-1']))
            ->assertRedirect();

        $location = (string) $response->headers->get('Location');

        $this->assertStringStartsWith(rtrim((string) $this->tenant->url, '/').'/integrations/discord/connect', $location);
        $this->assertStringContainsString('code=abc', $location);
    }

    public function test_the_central_callback_rejects_a_tampered_state(): void
    {
        $this->get(route('integrations.install.callback', ['provider' => 'discord', 'state' => 'not-a-state', 'code' => 'abc']))
            ->assertBadRequest();
    }

    public function test_connecting_links_the_server_to_the_organization(): void
    {
        Http::fake([
            'discord.com/api/oauth2/token' => Http::response(['access_token' => 'x', 'guild' => ['id' => 'guild-1', 'name' => '5th SFG Discord']]),
        ]);

        $this->actingAs($this->admin());

        [$state, $nonce] = $this->startInstall();

        $this->withSession(['integrations.discord.nonce' => $nonce])
            ->get(route('app.integrations.discord.connect', ['state' => $state, 'code' => 'abc']))
            ->assertRedirect();

        $settings = resolve(IntegrationSettings::class);
        $settings->refresh();

        $this->assertSame('guild-1', data_get($settings->discord_settings, 'discord_server'));
        $this->assertSame('5th SFG Discord', data_get($settings->discord_settings, 'discord_server_name'));
        $this->assertTrue(data_get($settings->discord_settings, 'discord_enabled'));
        $this->assertSame('guild-1', $this->tenant->refresh()->discord_guild_id);
    }

    public function test_a_server_connected_to_another_organization_cannot_be_claimed(): void
    {
        Tenant::factory()->createQuietly(['discord_guild_id' => 'guild-1']);

        Http::fake([
            'discord.com/api/oauth2/token' => Http::response(['access_token' => 'x', 'guild' => ['id' => 'guild-1', 'name' => 'Someone Else']]),
        ]);

        $this->actingAs($this->admin());

        [$state, $nonce] = $this->startInstall();

        $this->withSession(['integrations.discord.nonce' => $nonce])
            ->get(route('app.integrations.discord.connect', ['state' => $state, 'code' => 'abc']))
            ->assertRedirect();

        $this->assertNull($this->tenant->refresh()->discord_guild_id);
        $this->assertNull(data_get(resolve(IntegrationSettings::class)->discord_settings, 'discord_server'));
    }

    public function test_connecting_without_the_matching_session_is_rejected(): void
    {
        $this->actingAs($this->admin());

        [$state] = $this->startInstall();

        $this->withSession(['integrations.discord.nonce' => 'different'])
            ->get(route('app.integrations.discord.connect', ['state' => $state, 'code' => 'abc']))
            ->assertForbidden();
    }

    /**
     * @return array{0: string, 1: string}
     */
    protected function startInstall(): array
    {
        $state = IntegrationInstallService::state('discord');

        return [$state, (string) Session::get('integrations.discord.nonce')];
    }

    protected function admin(): User
    {
        $user = User::factory()->createQuietly();
        $user->assignRole(Utils::getSuperAdminName());

        return $user;
    }
}
