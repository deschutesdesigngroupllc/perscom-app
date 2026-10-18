<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Clusters\Settings\Pages;

use App\Filament\App\Clusters\Settings\Pages\Integration;
use App\Jobs\Tenant\SyncDiscordMember;
use App\Models\User;
use App\Settings\IntegrationSettings;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class IntegrationTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.discord.client_id' => 'client',
            'services.discord.client_secret' => 'secret',
            'services.slack.client_id' => 'client',
            'services.slack.client_secret' => 'secret',
        ]);

        $admin = User::factory()->createQuietly();
        $admin->assignRole(Utils::getSuperAdminName());
        $this->actingAs($admin);

        Http::fake([
            'discord.com/api/guilds/*/channels' => Http::response([['id' => 'chan-1', 'name' => 'announcements', 'type' => 0]]),
            'slack.com/api/conversations.list*' => Http::response(['ok' => true, 'channels' => [['id' => 'C1', 'name' => 'general']]]),
        ]);
    }

    public function test_it_shows_the_connected_server_and_workspace(): void
    {
        $this->connect();

        Livewire::test(Integration::class)
            ->assertOk()
            ->assertSee('5th SFG Discord')
            ->assertSee('5th SFG Slack')
            ->assertSee('/profile · /roster · /events · /rsvp');
    }

    public function test_it_does_not_list_servers_the_organization_has_not_connected(): void
    {
        Livewire::test(Integration::class)
            ->assertOk()
            ->assertSee('Not connected')
            ->assertSee('Connect Server')
            ->assertFormFieldHidden('discord_settings.discord_channel');

        Http::assertNotSent(fn ($request): bool => str_contains($request->url(), 'users/@me/guilds'));
    }

    public function test_saving_keeps_connection_details_and_queues_a_sync_when_role_sync_is_turned_on(): void
    {
        Queue::fake();

        $this->connect();

        $linked = User::factory()->createQuietly(['discord_user_id' => 'discord-jane']);

        Livewire::test(Integration::class)
            ->fillForm([
                'discord_settings.discord_channel' => 'chan-1',
                'discord_settings.sync_roles' => true,
                'slack_settings.slack_channel' => 'C1',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $settings = resolve(IntegrationSettings::class);
        $settings->refresh();

        $this->assertSame('guild-1', data_get($settings->discord_settings, 'discord_server'));
        $this->assertSame(['rank:1' => 'role-1'], data_get($settings->discord_settings, 'managed_roles'));
        $this->assertSame('chan-1', data_get($settings->discord_settings, 'discord_channel'));
        $this->assertSame('xoxb-test', Crypt::decryptString((string) data_get($settings->slack_settings, 'bot_token')));
        $this->assertSame('C1', data_get($settings->slack_settings, 'slack_channel'));

        Queue::assertPushed(SyncDiscordMember::class, fn (SyncDiscordMember $job): bool => $job->userId === $linked->getKey());
    }

    protected function connect(): void
    {
        $settings = resolve(IntegrationSettings::class);
        $settings->discord_settings = [
            'discord_enabled' => true,
            'discord_server' => 'guild-1',
            'discord_server_name' => '5th SFG Discord',
            'managed_roles' => ['rank:1' => 'role-1'],
        ];
        $settings->slack_settings = [
            'slack_enabled' => true,
            'team_id' => 'T1',
            'team_name' => '5th SFG Slack',
            'bot_token' => Crypt::encryptString('xoxb-test'),
        ];
        $settings->save();
    }
}
