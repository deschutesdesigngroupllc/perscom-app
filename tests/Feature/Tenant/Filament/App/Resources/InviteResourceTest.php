<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Resources;

use App\Filament\Actions\InviteAction;
use App\Filament\App\Resources\InviteResource\Pages\ListInvites;
use App\Filament\App\Resources\UserResource\Pages\ListUsers;
use App\Filament\App\Resources\UserResource\Pages\ViewUser;
use App\Models\Enums\InviteStatus;
use App\Models\Enums\InviteType;
use App\Models\Invite;
use App\Models\User;
use App\Notifications\User\Invited;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class InviteResourceTest extends TenantTestCase
{
    public function test_it_lists_invites(): void
    {
        $this->actingAsSuperAdmin();

        $invites = Invite::factory()->count(3)->createQuietly();

        Livewire::test(ListInvites::class)
            ->assertCanSeeTableRecords($invites);
    }

    public function test_it_sends_a_create_invite_from_the_invites_page(): void
    {
        Notification::fake();

        $admin = $this->actingAsSuperAdmin();
        $email = $this->faker->unique()->safeEmail();

        Livewire::test(ListInvites::class)
            ->callAction('invite', ['emails' => $email])
            ->assertHasNoActionErrors()
            ->assertNotified('Sent 1 Invitation');

        $this->assertDatabaseHas(Invite::class, [
            'email' => $email,
            'type' => InviteType::CREATE->value,
            'invited_by_id' => $admin->id,
        ]);

        Notification::assertSentOnDemandTimes(Invited::class, 1);
    }

    public function test_it_sends_a_create_invite_from_the_users_page(): void
    {
        Notification::fake();

        $this->actingAsSuperAdmin();
        $email = $this->faker->unique()->safeEmail();

        Livewire::test(ListUsers::class)
            ->callAction('invite', ['emails' => $email])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas(Invite::class, [
            'email' => $email,
            'type' => InviteType::CREATE->value,
        ]);
    }

    public function test_it_rejects_inviting_an_existing_users_email(): void
    {
        Notification::fake();

        $this->actingAsSuperAdmin();
        $existing = User::factory()->createQuietly();

        Livewire::test(ListInvites::class)
            ->callAction('invite', ['emails' => $existing->email])
            ->assertNotified('No Invitations Sent');

        Notification::assertNothingSent();
    }

    public function test_it_invites_several_people_at_once_and_reports_what_it_skipped(): void
    {
        Notification::fake();

        $this->actingAsSuperAdmin();
        $existing = User::factory()->createQuietly();

        Livewire::test(ListInvites::class)
            ->callAction('invite', ['emails' => "first@example.test, second@example.test\n{$existing->email}\nnot-an-email"])
            ->assertHasNoActionErrors()
            ->assertNotified('Sent 2 Invitations');

        $this->assertDatabaseHas(Invite::class, ['email' => 'first@example.test']);
        $this->assertDatabaseHas(Invite::class, ['email' => 'second@example.test']);
        $this->assertDatabaseMissing(Invite::class, ['email' => $existing->email]);
        Notification::assertSentOnDemandTimes(Invited::class, 2);
    }

    public function test_it_limits_how_many_people_can_be_invited_at_once(): void
    {
        $this->actingAsSuperAdmin();

        $emails = collect(range(1, InviteAction::MAX_INVITES + 1))->map(fn (int $i): string => "person{$i}@example.test")->implode(',');

        Livewire::test(ListInvites::class)
            ->callAction('invite', ['emails' => $emails])
            ->assertHasActionErrors(['emails']);
    }

    public function test_it_sends_a_claim_invite_for_a_profile_without_a_password(): void
    {
        Notification::fake();

        $this->actingAsSuperAdmin();
        $profile = User::factory()->createQuietly(['password' => null]);
        $email = $this->faker->unique()->safeEmail();

        Livewire::test(ViewUser::class, ['record' => $profile->getKey()])
            ->assertActionVisible('sendInvite')
            ->assertActionDataSet(['email' => $profile->email])
            ->callAction('sendInvite', ['email' => $email])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas(Invite::class, [
            'email' => $email,
            'user_id' => $profile->id,
            'type' => InviteType::CLAIM->value,
        ]);
    }

    public function test_the_claim_action_is_hidden_for_a_profile_that_is_already_owned(): void
    {
        $this->actingAsSuperAdmin();
        $profile = User::factory()->createQuietly();

        Livewire::test(ViewUser::class, ['record' => $profile->getKey()])
            ->assertActionHidden('sendInvite');

        Livewire::test(ListUsers::class)
            ->assertActionHidden(TestAction::make('sendInvite')->table($profile));
    }

    public function test_it_revokes_a_pending_invite(): void
    {
        $this->actingAsSuperAdmin();
        $invite = Invite::factory()->createQuietly();

        Livewire::test(ListInvites::class)
            ->callAction(TestAction::make('revoke')->table($invite))
            ->assertNotified('The invitation has been revoked.');

        $this->assertSame(InviteStatus::REVOKED, $invite->refresh()->status);
    }

    public function test_it_resends_an_expired_invite(): void
    {
        Notification::fake();

        $this->actingAsSuperAdmin();
        $invite = Invite::factory()->expired()->createQuietly();

        Livewire::test(ListInvites::class)
            ->assertActionHidden(TestAction::make('revoke')->table($invite))
            ->callAction(TestAction::make('resend')->table($invite))
            ->assertNotified('The invitation has been resent.');

        $this->assertSame(InviteStatus::PENDING, $invite->refresh()->status);

        Notification::assertSentOnDemandTimes(Invited::class, 1);
    }

    public function test_resending_a_claim_invite_revokes_newer_invites_for_the_profile(): void
    {
        Notification::fake();

        $this->actingAsSuperAdmin();
        $profile = User::factory()->createQuietly(['password' => null]);
        $invite = Invite::factory()->claim($profile)->revoked()->createQuietly();
        $newer = Invite::factory()->claim($profile)->createQuietly();

        Livewire::test(ListInvites::class)
            ->callAction(TestAction::make('resend')->table($invite))
            ->assertNotified('The invitation has been resent.');

        $this->assertSame(InviteStatus::PENDING, $invite->refresh()->status);
        $this->assertSame(InviteStatus::REVOKED, $newer->refresh()->status);
    }

    public function test_claim_invites_for_an_already_claimed_profile_cannot_be_resent(): void
    {
        $this->actingAsSuperAdmin();
        $profile = User::factory()->createQuietly();
        $invite = Invite::factory()->claim($profile)->revoked()->createQuietly();

        Livewire::test(ListInvites::class)
            ->assertActionHidden(TestAction::make('resend')->table($invite));
    }

    public function test_accepted_invites_cannot_be_resent_or_revoked(): void
    {
        $this->actingAsSuperAdmin();
        $invite = Invite::factory()->accepted()->createQuietly();

        Livewire::test(ListInvites::class)
            ->assertActionHidden(TestAction::make('resend')->table($invite))
            ->assertActionHidden(TestAction::make('revoke')->table($invite));
    }

    public function test_users_without_permission_cannot_access_invites(): void
    {
        $this->actingAs(User::factory()->createQuietly());

        $this->get(ListInvites::getUrl())->assertForbidden();
    }

    private function actingAsSuperAdmin(): User
    {
        $user = User::factory()->createQuietly();
        $user->assignRole(Utils::getSuperAdminName());

        $this->actingAs($user);

        return $user;
    }
}
