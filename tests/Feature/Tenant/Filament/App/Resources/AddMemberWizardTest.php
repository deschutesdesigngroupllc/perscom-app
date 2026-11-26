<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Resources;

use App\Filament\App\Resources\UserResource;
use App\Filament\App\Resources\UserResource\Pages\CreateUser;
use App\Models\Enums\InviteType;
use App\Models\Enums\RosterMode;
use App\Models\Invite;
use App\Models\Position;
use App\Models\Rank;
use App\Models\Slot;
use App\Models\Unit;
use App\Models\User;
use App\Notifications\User\Invited;
use App\Services\SettingsService;
use App\Settings\DashboardSettings;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use ReflectionMethod;
use Tests\Feature\Tenant\TenantTestCase;

class AddMemberWizardTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->createQuietly();
        $admin->assignRole(Utils::getSuperAdminName());
        $this->actingAs($admin);
    }

    public function test_adding_a_member_starts_their_history_and_sends_an_invite(): void
    {
        Notification::fake();

        $unit = Unit::factory()->createQuietly();
        $rank = Rank::factory()->createQuietly();

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Jordan Reyes',
                'email' => 'jordan.reyes@example.test',
                'send_invite' => true,
                'unit_id' => $unit->getKey(),
                'rank_id' => $rank->getKey(),
            ])
            ->call('create')
            ->assertHasNoErrors();

        $member = User::query()->where('email', 'jordan.reyes@example.test')->firstOrFail();

        $this->assertTrue($member->approved);
        $this->assertSame($rank->getKey(), $member->rank_id);
        $this->assertSame($unit->getKey(), $member->unit_id);
        $this->assertDatabaseHas('records_ranks', ['user_id' => $member->getKey(), 'rank_id' => $rank->getKey()]);
        $this->assertDatabaseHas('records_assignments', ['user_id' => $member->getKey(), 'unit_id' => $unit->getKey()]);
        $this->assertTrue(Invite::query()->pending()->where('user_id', $member->getKey())->where('type', InviteType::CLAIM)->exists());
        Notification::assertSentOnDemand(Invited::class);
    }

    public function test_manual_roster_places_new_members_into_a_slot(): void
    {
        DashboardSettings::fake(['roster_mode' => RosterMode::MANUAL]);
        SettingsService::flush(DashboardSettings::class);

        $unit = Unit::factory()->createQuietly();
        $position = Position::factory()->createQuietly();
        $slot = Slot::factory()->createQuietly(['position_id' => $position->getKey()]);
        $unit->slots()->attach($slot);
        $unitSlotId = DB::table('units_slots')->where('unit_id', $unit->getKey())->value('id');

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Slot Member',
                'email' => 'slot.member@example.test',
                'send_invite' => false,
                'unit_slot_id' => $unitSlotId,
            ])
            ->call('create')
            ->assertHasNoErrors();

        $member = User::query()->where('email', 'slot.member@example.test')->firstOrFail();

        $this->assertDatabaseHas('records_assignments', [
            'user_id' => $member->getKey(),
            'unit_slot_id' => $unitSlotId,
            'unit_id' => $unit->getKey(),
            'position_id' => $position->getKey(),
        ]);
    }

    public function test_placement_is_optional_and_no_invite_is_sent_when_unchecked(): void
    {
        Notification::fake();

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Sam Ortiz',
                'email' => 'sam.ortiz@example.test',
                'send_invite' => false,
            ])
            ->call('create')
            ->assertHasNoErrors()
            ->assertRedirect(UserResource::getUrl('view', ['record' => User::query()->where('email', 'sam.ortiz@example.test')->firstOrFail()]));

        Notification::assertNothingSent();
    }

    public function test_the_wizard_keeps_its_steps_fields_and_buttons_in_one_card(): void
    {
        Livewire::test(CreateUser::class)
            ->assertOk()
            ->assertSeeHtml('fi-sc-wizard fi-contained');
    }

    public function test_email_must_be_unique(): void
    {
        User::factory()->createQuietly(['email' => 'taken@example.test']);

        Livewire::test(CreateUser::class)
            ->fillForm(['name' => 'Someone', 'email' => 'taken@example.test'])
            ->call('create')
            ->assertHasFormErrors(['email' => 'unique']);
    }

    public function test_placement_fields_are_hidden_from_creators_who_cannot_record_them(): void
    {
        $creator = User::factory()->createQuietly()->syncRoles([])->givePermissionTo(['view_any_user', 'create_user']);
        $this->actingAs($creator);

        Livewire::test(CreateUser::class)
            ->assertFormFieldHidden('rank_id')
            ->assertFormFieldHidden('status_id')
            ->assertFormFieldHidden('unit_id')
            ->assertFormFieldHidden('position_id');
    }

    public function test_no_member_is_created_when_their_starting_records_are_refused(): void
    {
        $creator = User::factory()->createQuietly()->syncRoles([])->givePermissionTo(['view_any_user', 'create_user']);
        $this->actingAs($creator);
        $rank = Rank::factory()->createQuietly();

        $page = Livewire::test(CreateUser::class)->instance();
        $create = new ReflectionMethod($page, 'handleRecordCreation');

        try {
            $create->invoke($page, ['name' => 'New Recruit', 'email' => 'recruit@example.test', 'rank_id' => $rank->getKey()]);
            $this->fail('Creating the rank record should have been refused.');
        } catch (AuthorizationException) {
            $this->assertDatabaseMissing(User::class, ['email' => 'recruit@example.test']);
        }
    }
}
