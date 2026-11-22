<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\App\Integrations;

use App\Filament\App\Clusters\Settings\Pages\Integration;
use App\Filament\App\Pages\Dashboard;
use App\Models\Tenant;
use App\Models\User;
use App\Services\IntegrationInstallService;
use App\Settings\IntegrationSettings;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Crypt;
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
            ->assertRedirect(Integration::getUrl(['tab' => 'discord::data::tab']));

        $this->assertNotificationBodyContains('opened in a different browser');
        $this->assertNull($this->tenant->refresh()->discord_guild_id);
    }

    public function test_cancelling_in_discord_explains_what_happened(): void
    {
        $this->actingAs($this->admin());

        [$state, $nonce] = $this->startInstall();

        $this->withSession(['integrations.discord.nonce' => $nonce])
            ->get(route('app.integrations.discord.connect', ['state' => $state, 'error' => 'access_denied']))
            ->assertRedirect(Integration::getUrl(['tab' => 'discord::data::tab']));

        $this->assertNotificationBodyContains('You cancelled the authorization in Discord');
    }

    public function test_an_unreachable_discord_is_reported_instead_of_erroring(): void
    {
        Http::fake(fn () => throw new ConnectionException('timed out'));

        $this->actingAs($this->admin());

        [$state, $nonce] = $this->startInstall();

        $this->withSession(['integrations.discord.nonce' => $nonce])
            ->get(route('app.integrations.discord.connect', ['state' => $state, 'code' => 'abc']))
            ->assertRedirect(Integration::getUrl(['tab' => 'discord::data::tab']));

        $this->assertNotificationBodyContains("Discord couldn't be reached");
    }

    public function test_members_are_sent_to_the_dashboard_with_an_explanation(): void
    {
        $this->actingAs(User::factory()->withoutRoles()->createQuietly());

        $this->get(route('app.integrations.discord.connect', ['state' => 'x', 'code' => 'abc']))
            ->assertRedirect(Dashboard::getUrl());

        $this->assertNotificationBodyContains('Only the organization owner can connect Discord');
    }

    public function test_the_callback_returns_to_the_host_the_install_started_on(): void
    {
        $this->tenant->forceFill(['custom_url' => 'https://roster.example.test'])->saveQuietly();

        $state = Crypt::encrypt(['provider' => 'discord', 'tenant' => $this->tenant->getKey(), 'nonce' => 'n', 'origin' => rtrim((string) $this->tenant->fallback_url, '/')]);

        $response = $this->get(route('integrations.install.callback', ['provider' => 'discord', 'state' => $state, 'code' => 'abc']))
            ->assertRedirect();

        $this->assertStringStartsWith(rtrim((string) $this->tenant->fallback_url, '/').'/integrations/discord/connect', (string) $response->headers->get('Location'));
    }

    public function test_the_callback_ignores_an_origin_outside_the_organization(): void
    {
        $state = Crypt::encrypt(['provider' => 'discord', 'tenant' => $this->tenant->getKey(), 'nonce' => 'n', 'origin' => 'https://evil.example.test']);

        $response = $this->get(route('integrations.install.callback', ['provider' => 'discord', 'state' => $state, 'code' => 'abc']))
            ->assertRedirect();

        $this->assertStringStartsNotWith('https://evil.example.test', (string) $response->headers->get('Location'));
    }

    protected function assertNotificationBodyContains(string $text): void
    {
        $bodies = collect(session('filament.notifications', []))->pluck('body')->implode(' ');

        $this->assertStringContainsString($text, $bodies);
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
