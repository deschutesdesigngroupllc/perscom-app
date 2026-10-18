<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Integrations;

use App\Models\Enums\EventRegistrationStatus;
use App\Models\Enums\ScheduleEndType;
use App\Models\Enums\ScheduleFrequency;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Schedule;
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

    public function test_private_profiles_are_not_shown_to_linked_members_without_permission(): void
    {
        $this->setPublicProfiles(false);

        User::factory()->createQuietly(['approved' => true, 'discord_user_id' => 'discord-member']);
        User::factory()->withoutRoles()->createQuietly(['approved' => true, 'discord_user_id' => 'discord-recruit']);

        $this->interact($this->command('profile', [['name' => 'member', 'type' => 6, 'value' => 'discord-member']], invoker: 'discord-recruit'))
            ->assertJsonPath('data.content', 'That member’s service profile is private.');
    }

    public function test_private_profiles_are_shown_to_members_who_can_view_them(): void
    {
        $this->setPublicProfiles(false);
        $this->linkedMember();

        User::factory()->createQuietly(['name' => 'Jabari Willms', 'approved' => true, 'discord_user_id' => 'discord-member']);

        $this->interact($this->command('profile', [['name' => 'member', 'type' => 6, 'value' => 'discord-member']], invoker: 'discord-admin'))
            ->assertJsonPath('data.embeds.0.title', 'Jabari Willms')
            ->assertJsonPath('data.flags', 64);
    }

    public function test_members_can_see_their_own_private_profile(): void
    {
        $this->setPublicProfiles(false);

        User::factory()->createQuietly(['name' => 'Jabari Willms', 'approved' => true, 'discord_user_id' => 'discord-recruit']);

        $this->interact($this->command('profile', invoker: 'discord-recruit'))
            ->assertJsonPath('data.embeds.0.title', 'Jabari Willms')
            ->assertJsonPath('data.flags', 64);
    }

    public function test_rsvp_to_a_repeating_event_records_a_response_for_the_selected_date(): void
    {
        $this->travelTo(Date::parse('2026-06-20 12:00:00'));

        $member = $this->linkedMember();
        $event = $this->repeatingEvent();
        $selected = Date::parse('2026-06-25 20:00:00');

        $this->interact($this->command('rsvp', [
            ['name' => 'event', 'type' => 3, 'value' => $event->getKey().'|'.$selected->getTimestamp()],
            ['name' => 'status', 'type' => 3, 'value' => 'not_going'],
        ]))->assertJsonPath('data.content', sprintf('You’re marked **not going** for **Weekly Drill** on <t:%d:F>.', $selected->getTimestamp()));

        $registration = EventRegistration::query()->where('event_id', $event->getKey())->where('user_id', $member->getKey())->sole();

        $this->assertSame(EventRegistrationStatus::NOT_GOING, $registration->status);
        $this->assertSame(Date::parse('2026-06-25 20:00:00')->getTimestamp(), $registration->occurrence?->starts->getTimestamp());
    }

    public function test_rsvp_rejects_statuses_members_cannot_choose(): void
    {
        $member = $this->linkedMember();
        $event = Event::factory()->createQuietly([
            'starts' => now()->addDays(3),
            'ends' => now()->addDays(3)->addHours(4),
            'registration_enabled' => true,
            'registration_deadline' => null,
        ]);

        $this->interact($this->command('rsvp', [
            ['name' => 'event', 'type' => 3, 'value' => (string) $event->getKey()],
            ['name' => 'status', 'type' => 3, 'value' => 'attended'],
        ]))->assertJsonPath('data.content', 'That is not a valid response for this event.');

        $this->assertDatabaseMissing('events_registrations', ['event_id' => $event->getKey(), 'user_id' => $member->getKey()]);
    }

    public function test_rsvp_to_an_ended_event_is_refused(): void
    {
        $member = $this->linkedMember();
        $event = Event::factory()->createQuietly([
            'starts' => now()->subDays(3),
            'ends' => now()->subDays(3)->addHour(),
            'all_day' => false,
            'registration_enabled' => true,
            'registration_deadline' => null,
        ]);

        $this->interact($this->command('rsvp', [
            ['name' => 'event', 'type' => 3, 'value' => $event->getKey().'|'.$event->starts->getTimestamp()],
        ]))->assertJsonPath('data.content', 'This event has already ended.');

        $this->assertDatabaseMissing('events_registrations', ['event_id' => $event->getKey(), 'user_id' => $member->getKey()]);
    }

    public function test_events_lists_upcoming_dates_of_repeating_events(): void
    {
        $this->travelTo(Date::parse('2026-06-20 12:00:00'));

        $this->linkedMember();
        $this->repeatingEvent();

        $this->interact($this->command('events'))
            ->assertJsonPath('data.embeds.0.fields.0.name', 'Weekly Drill')
            ->assertJsonPath('data.embeds.0.fields.0.value', sprintf("<t:%d:F>\nNot RSVP’d", Date::parse('2026-06-25 20:00:00')->getTimestamp()));
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

    public function test_event_autocomplete_lists_upcoming_dates_of_repeating_events(): void
    {
        $this->travelTo(Date::parse('2026-06-20 12:00:00'));

        $event = $this->repeatingEvent();

        $response = $this->interact([
            'type' => 4,
            'guild_id' => 'guild-1',
            'member' => ['user' => ['id' => 'stranger']],
            'data' => ['name' => 'rsvp', 'options' => [['name' => 'event', 'type' => 3, 'value' => 'drill', 'focused' => true]]],
        ])->assertJsonPath('type', 8);

        $this->assertSame('Weekly Drill · Jun 25', $response->json('data.choices.0.name'));
        $this->assertSame($event->getKey().'|'.Date::parse('2026-06-25 20:00:00')->getTimestamp(), $response->json('data.choices.0.value'));
    }

    protected function repeatingEvent(): Event
    {
        $start = Date::parse('2026-06-04 20:00:00');
        $event = Event::factory()->createQuietly(['name' => 'Weekly Drill', 'repeats' => true, 'all_day' => false, 'starts' => $start, 'ends' => $start->copy()->addHour(), 'registration_enabled' => true, 'registration_deadline' => null]);
        Schedule::factory()->for($event, 'repeatable')->createQuietly(['start' => $start, 'duration' => 1, 'frequency' => ScheduleFrequency::WEEKLY, 'interval' => 1, 'end_type' => ScheduleEndType::NEVER]);

        return $event;
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
