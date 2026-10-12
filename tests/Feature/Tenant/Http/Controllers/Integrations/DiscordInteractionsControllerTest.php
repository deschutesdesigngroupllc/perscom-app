<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Integrations;

use App\Models\Enums\EventRegistrationStatus;
use App\Models\Event;
use App\Models\User;
use App\Services\SettingsService;
use App\Settings\DashboardSettings;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Support\Facades\Date;
use Illuminate\Testing\TestResponse;
use Tests\Feature\Tenant\TenantTestCase;

class DiscordInteractionsControllerTest extends TenantTestCase
{
    protected string $secretKey;

    protected function setUp(): void
    {
        parent::setUp();

        $keypair = sodium_crypto_sign_keypair();
        $this->secretKey = sodium_crypto_sign_secretkey($keypair);

        config(['services.discord.public_key' => bin2hex(sodium_crypto_sign_publickey($keypair))]);

        $this->tenant->forceFill(['discord_guild_id' => 'guild-1'])->save();
    }

    public function test_unsigned_requests_are_rejected(): void
    {
        $this->call('POST', route('integrations.discord.interactions'), content: json_encode(['type' => 1]))
            ->assertUnauthorized();
    }

    public function test_requests_with_a_bad_signature_are_rejected(): void
    {
        $body = (string) json_encode(['type' => 1]);

        $this->call('POST', route('integrations.discord.interactions'), server: [
            'HTTP_X_SIGNATURE_ED25519' => str_repeat('a', 128),
            'HTTP_X_SIGNATURE_TIMESTAMP' => (string) Date::now()->getTimestamp(),
        ], content: $body)->assertUnauthorized();
    }

    public function test_ping_is_answered_with_pong(): void
    {
        $this->interact(['type' => 1])->assertOk()->assertExactJson(['type' => 1]);
    }

    public function test_servers_that_are_not_connected_are_told_so(): void
    {
        $this->interact($this->command('roster', guildId: 'someone-elses-guild'))
            ->assertOk()
            ->assertJsonPath('data.flags', 64)
            ->assertJsonPath('data.content', 'This Discord server is not connected to a PERSCOM organization. An administrator can connect it from Settings → Integrations.');
    }

    public function test_profile_shows_a_public_profile_to_the_server(): void
    {
        $this->setPublicProfiles(true);

        $member = User::factory()->createQuietly(['name' => 'Jabari Willms', 'approved' => true, 'discord_user_id' => 'discord-member']);

        $response = $this->interact($this->command('profile', [['name' => 'member', 'type' => 6, 'value' => 'discord-member']], invoker: 'stranger'))
            ->assertOk()
            ->assertJsonPath('type', 4)
            ->assertJsonPath('data.embeds.0.title', 'Jabari Willms')
            ->assertJsonMissingPath('data.flags');

        $this->assertStringContainsString('/card.png', (string) $response->json('data.embeds.0.image.url'));
        $this->assertStringContainsString((string) $member->refresh()->public_profile_key, (string) $response->json('data.embeds.0.url'));
    }

    public function test_private_profiles_are_not_shown_to_unlinked_discord_users(): void
    {
        $this->setPublicProfiles(false);

        User::factory()->createQuietly(['approved' => true, 'discord_user_id' => 'discord-member']);

        $this->interact($this->command('profile', [['name' => 'member', 'type' => 6, 'value' => 'discord-member']], invoker: 'stranger'))
            ->assertJsonPath('data.content', 'That member’s service profile is private.')
            ->assertJsonPath('data.flags', 64);
    }

    public function test_rsvp_registers_the_linked_member(): void
    {
        $member = $this->linkedMember();
        $event = Event::factory()->createQuietly([
            'name' => 'Exercise Robin Sage',
            'starts' => now()->addDays(3),
            'ends' => now()->addDays(3)->addHours(4),
            'registration_enabled' => true,
            'registration_deadline' => null,
        ]);

        $this->interact($this->command('rsvp', [
            ['name' => 'event', 'type' => 3, 'value' => (string) $event->getKey()],
            ['name' => 'status', 'type' => 3, 'value' => 'tentative'],
        ], invoker: 'discord-admin'))
            ->assertJsonPath('data.content', 'You’re marked **tentative** for **Exercise Robin Sage**.');

        $this->assertDatabaseHas('events_registrations', [
            'event_id' => $event->getKey(),
            'user_id' => $member->getKey(),
            'status' => EventRegistrationStatus::TENTATIVE->value,
        ]);
    }

    public function test_commands_require_a_linked_account(): void
    {
        $this->interact($this->command('events', invoker: 'stranger'))
            ->assertJsonPath('data.content', 'Link your Discord account from your PERSCOM profile to use this command.');
    }

    public function test_commands_respect_permissions(): void
    {
        User::factory()->createQuietly(['approved' => true, 'discord_user_id' => 'discord-recruit']);

        $this->interact($this->command('events', invoker: 'discord-recruit'))
            ->assertJsonPath('data.content', 'You do not have permission to do that in PERSCOM.');
    }

    public function test_event_autocomplete_lists_matching_upcoming_events(): void
    {
        Event::factory()->createQuietly(['name' => 'Exercise Robin Sage', 'starts' => now()->addDay(), 'ends' => now()->addDay()->addHour()]);
        Event::factory()->createQuietly(['name' => 'Range Day', 'starts' => now()->addDay(), 'ends' => now()->addDay()->addHour()]);
        Event::factory()->createQuietly(['name' => 'Robin Sage Debrief', 'starts' => now()->subDays(3), 'ends' => now()->subDays(3)->addHour()]);

        $response = $this->interact([
            'type' => 4,
            'guild_id' => 'guild-1',
            'member' => ['user' => ['id' => 'stranger']],
            'data' => ['name' => 'rsvp', 'options' => [['name' => 'event', 'type' => 3, 'value' => 'robin', 'focused' => true]]],
        ])->assertJsonPath('type', 8);

        $this->assertCount(1, $response->json('data.choices'));
        $this->assertStringStartsWith('Exercise Robin Sage', $response->json('data.choices.0.name'));
    }

    protected function linkedMember(): User
    {
        $member = User::factory()->createQuietly(['approved' => true, 'discord_user_id' => 'discord-admin']);
        $member->assignRole(Utils::getSuperAdminName());

        return $member;
    }

    protected function setPublicProfiles(bool $enabled): void
    {
        DashboardSettings::fake(['public_profiles_enabled' => $enabled, 'user_hidden_fields' => []]);

        SettingsService::flush(DashboardSettings::class);
    }

    /**
     * @param  array<int, array<string, mixed>>  $options
     * @return array<string, mixed>
     */
    protected function command(string $name, array $options = [], string $invoker = 'discord-admin', string $guildId = 'guild-1'): array
    {
        return [
            'type' => 2,
            'guild_id' => $guildId,
            'member' => ['user' => ['id' => $invoker]],
            'data' => ['name' => $name, 'options' => $options],
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function interact(array $payload): TestResponse
    {
        $body = (string) json_encode($payload);
        $timestamp = (string) Date::now()->getTimestamp();

        return $this->call('POST', route('integrations.discord.interactions'), server: [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_SIGNATURE_ED25519' => bin2hex(sodium_crypto_sign_detached($timestamp.$body, $this->secretKey)),
            'HTTP_X_SIGNATURE_TIMESTAMP' => $timestamp,
        ], content: $body);
    }
}
