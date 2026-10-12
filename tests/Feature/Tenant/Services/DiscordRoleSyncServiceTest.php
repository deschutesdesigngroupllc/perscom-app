<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Services;

use App\Jobs\Tenant\SyncDiscordMember;
use App\Models\Rank;
use App\Models\Unit;
use App\Models\User;
use App\Services\DiscordRoleSyncService;
use App\Settings\IntegrationSettings;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\Tenant\TenantTestCase;

class DiscordRoleSyncServiceTest extends TenantTestCase
{
    public function test_sync_swaps_managed_roles_and_sets_the_nickname(): void
    {
        $rank = Rank::factory()->createQuietly(['name' => 'Sergeant', 'abbreviation' => 'SGT']);
        $unit = Unit::factory()->createQuietly(['name' => 'Alpha Company']);

        $this->configure(['sync_roles' => true, 'sync_nicknames' => true, 'managed_roles' => [
            'rank:999' => 'old-rank-role',
            'unit:'.$unit->getKey() => 'unit-role',
        ]]);

        $user = User::factory()->createQuietly([
            'name' => 'Jane Doe',
            'rank_id' => $rank->getKey(),
            'unit_id' => $unit->getKey(),
            'discord_user_id' => 'discord-jane',
        ]);

        Http::fake([
            'discord.com/api/guilds/guild-1/members/discord-jane/roles/*' => Http::response(status: 204),
            'discord.com/api/guilds/guild-1/members/discord-jane' => fn (Request $request) => $request->method() === 'GET'
                ? Http::response(['roles' => ['old-rank-role', 'unit-role', 'server-booster']])
                : Http::response(['nick' => 'SGT Jane Doe']),
            'discord.com/api/guilds/guild-1/roles' => fn (Request $request) => $request->method() === 'GET'
                ? Http::response([['id' => 'unit-role', 'name' => 'Alpha Company']])
                : Http::response(['id' => 'sergeant-role', 'name' => 'Sergeant']),
        ]);

        DiscordRoleSyncService::sync($user);

        Http::assertSent(fn (Request $request): bool => $request->method() === 'POST' && str_ends_with($request->url(), 'guilds/guild-1/roles') && $request['name'] === 'Sergeant');
        Http::assertSent(fn (Request $request): bool => $request->method() === 'PUT' && str_ends_with($request->url(), 'roles/sergeant-role'));
        Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE' && str_ends_with($request->url(), 'roles/old-rank-role'));
        Http::assertNotSent(fn (Request $request): bool => $request->method() === 'DELETE' && str_ends_with($request->url(), 'roles/server-booster'));
        Http::assertNotSent(fn (Request $request): bool => $request->method() === 'POST' && $request['name'] === 'Alpha Company');
        Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH' && $request['nick'] === 'SGT Jane Doe');

        $this->assertSame('sergeant-role', data_get(resolve(IntegrationSettings::class)->discord_settings, 'managed_roles.rank:'.$rank->getKey()));
    }

    public function test_members_who_are_not_in_the_server_are_skipped(): void
    {
        $this->configure(['sync_roles' => true]);

        $user = User::factory()->createQuietly(['discord_user_id' => 'discord-gone']);

        Http::fake(['discord.com/api/guilds/guild-1/members/discord-gone' => Http::response(['message' => 'Unknown Member'], 404)]);

        DiscordRoleSyncService::sync($user);

        Http::assertSentCount(1);
    }

    public function test_promotions_queue_a_sync_when_it_is_enabled(): void
    {
        Queue::fake();

        $this->configure(['sync_roles' => true]);

        $user = User::factory()->createQuietly(['discord_user_id' => 'discord-jane']);
        $user->update(['rank_id' => Rank::factory()->createQuietly()->getKey()]);

        Queue::assertPushed(SyncDiscordMember::class, fn (SyncDiscordMember $job): bool => $job->userId === $user->getKey());
    }

    public function test_nothing_is_queued_when_sync_is_off(): void
    {
        Queue::fake();

        $this->configure([]);

        $user = User::factory()->createQuietly(['discord_user_id' => 'discord-jane']);
        $user->update(['rank_id' => Rank::factory()->createQuietly()->getKey()]);

        Queue::assertNotPushed(SyncDiscordMember::class);
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    protected function configure(array $settings): void
    {
        $integration = resolve(IntegrationSettings::class);
        $integration->discord_settings = ['discord_enabled' => true, 'discord_server' => 'guild-1', ...$settings];
        $integration->save();
    }
}
