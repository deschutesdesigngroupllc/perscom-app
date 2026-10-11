<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Invites;

use App\Actions\Invites\AcceptInvite;
use App\Models\Enums\InviteStatus;
use App\Models\Invite;
use App\Models\User;
use App\Notifications\User\AdminApprovalRequired;
use App\Notifications\User\ApprovalRequired;
use App\Notifications\User\PasswordChanged;
use App\Settings\RegistrationSettings;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\Tenant\TenantTestCase;

class AcceptInviteTest extends TenantTestCase
{
    /**
     * @return array<string, array{string}>
     */
    public static function invalidInviteProvider(): array
    {
        return [
            'expired' => ['expired'],
            'revoked' => ['revoked'],
            'accepted' => ['accepted'],
        ];
    }

    public function test_a_create_invite_registers_an_approved_and_verified_user(): void
    {
        Notification::fake();

        RegistrationSettings::fake([
            'admin_approval_required' => true,
        ]);

        $admin = User::factory()->createQuietly();
        $admin->assignRole(Utils::getSuperAdminName());

        $invite = Invite::factory()->createQuietly();

        $user = AcceptInvite::handle($invite, [
            'name' => 'Jane Doe',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertSame('Jane Doe', $user->name);
        $this->assertSame($invite->email, $user->email);
        $this->assertTrue(Hash::check('Password123!', $user->password));
        $this->assertTrue($user->refresh()->approved);
        $this->assertNotNull($user->email_verified_at);

        $invite->refresh();
        $this->assertSame(InviteStatus::ACCEPTED, $invite->status);
        $this->assertSame($user->id, $invite->user_id);

        Notification::assertNotSentTo($user, ApprovalRequired::class);
        Notification::assertNotSentTo($admin, AdminApprovalRequired::class);
    }

    public function test_a_claim_invite_transfers_ownership_of_the_profile(): void
    {
        Notification::fake();

        $profile = User::factory()->unverified()->createQuietly([
            'password' => null,
            'approved' => false,
        ]);

        $invite = Invite::factory()->claim($profile)->createQuietly();

        $user = AcceptInvite::handle($invite, [
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertTrue($user->is($profile));

        $profile->refresh();
        $this->assertSame($invite->email, $profile->email);
        $this->assertTrue(Hash::check('Password123!', $profile->password));
        $this->assertTrue($profile->approved);
        $this->assertNotNull($profile->email_verified_at);
        $this->assertSame(InviteStatus::ACCEPTED, $invite->refresh()->status);

        Notification::assertNotSentTo($profile, PasswordChanged::class);
    }

    public function test_accepting_revokes_other_outstanding_invites(): void
    {
        Notification::fake();

        $profile = User::factory()->createQuietly(['password' => null]);
        $invite = Invite::factory()->claim($profile)->createQuietly();
        $other = Invite::factory()->claim($profile)->createQuietly();

        AcceptInvite::handle($invite, [
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertSame(InviteStatus::REVOKED, $other->refresh()->status);
    }

    #[DataProvider('invalidInviteProvider')]
    public function test_it_rejects_an_invite_that_is_no_longer_pending(string $state): void
    {
        $invite = Invite::factory()->{$state}()->createQuietly();

        $this->expectException(ValidationException::class);

        AcceptInvite::handle($invite, [
            'name' => 'Jane Doe',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);
    }

    public function test_it_rejects_a_create_invite_when_the_email_has_since_been_registered(): void
    {
        $invite = Invite::factory()->createQuietly();
        User::factory()->createQuietly(['email' => $invite->email]);

        $this->expectException(ValidationException::class);

        AcceptInvite::handle($invite, [
            'name' => 'Jane Doe',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);
    }

    public function test_it_requires_a_confirmed_password(): void
    {
        $invite = Invite::factory()->createQuietly();

        try {
            AcceptInvite::handle($invite, [
                'name' => 'Jane Doe',
                'password' => 'Password123!',
                'password_confirmation' => 'Different123!',
            ]);

            $this->fail('Expected a validation exception.');
        } catch (ValidationException $validationException) {
            $this->assertArrayHasKey('password', $validationException->errors());
        }

        $this->assertTrue($invite->refresh()->isPending());
    }
}
