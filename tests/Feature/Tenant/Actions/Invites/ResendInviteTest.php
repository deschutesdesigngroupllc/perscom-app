<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Invites;

use App\Actions\Invites\ResendInvite;
use App\Models\Enums\InviteStatus;
use App\Models\Invite;
use App\Models\User;
use App\Notifications\User\Invited;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Tests\Feature\Tenant\TenantTestCase;

class ResendInviteTest extends TenantTestCase
{
    public function test_it_reactivates_a_revoked_invite_and_delivers_a_new_link(): void
    {
        Notification::fake();

        $invite = Invite::factory()->revoked()->createQuietly();

        ResendInvite::handle($invite);

        $this->assertSame(InviteStatus::PENDING, $invite->refresh()->status);

        Notification::assertSentOnDemandTimes(Invited::class, 1);
    }

    public function test_resending_a_claim_invite_revokes_other_pending_invites_for_the_profile(): void
    {
        Notification::fake();

        $profile = User::factory()->createQuietly(['password' => null]);
        $invite = Invite::factory()->claim($profile)->revoked()->createQuietly();
        $newer = Invite::factory()->claim($profile)->createQuietly();

        ResendInvite::handle($invite);

        $this->assertSame(InviteStatus::PENDING, $invite->refresh()->status);
        $this->assertSame(InviteStatus::REVOKED, $newer->refresh()->status);
    }

    public function test_it_refuses_to_resend_a_claim_invite_for_a_profile_that_has_been_claimed(): void
    {
        Notification::fake();

        $profile = User::factory()->createQuietly();
        $invite = Invite::factory()->claim($profile)->revoked()->createQuietly();

        try {
            ResendInvite::handle($invite);

            $this->fail('Expected a validation exception.');
        } catch (ValidationException $validationException) {
            $this->assertSame('This profile has already been claimed.', $validationException->validator->errors()->first());
        }

        $this->assertSame(InviteStatus::REVOKED, $invite->refresh()->status);

        Notification::assertNothingSent();
    }

    public function test_it_refuses_to_resend_an_accepted_invite(): void
    {
        Notification::fake();

        $invite = Invite::factory()->accepted()->createQuietly();

        $this->expectException(ValidationException::class);

        ResendInvite::handle($invite);
    }
}
