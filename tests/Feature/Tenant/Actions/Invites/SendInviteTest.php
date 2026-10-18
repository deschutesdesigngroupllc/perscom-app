<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Invites;

use App\Actions\Invites\SendInvite;
use App\Models\Enums\InviteStatus;
use App\Models\Enums\InviteType;
use App\Models\Invite;
use App\Models\User;
use App\Notifications\User\Invited;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Tests\Feature\Tenant\TenantTestCase;

class SendInviteTest extends TenantTestCase
{
    public function test_it_creates_and_delivers_a_create_invite(): void
    {
        Notification::fake();

        $admin = User::factory()->createQuietly();
        $email = $this->faker->unique()->safeEmail();

        $invite = SendInvite::handle(InviteType::CREATE, $email, 'John Doe', invitedBy: $admin);

        $this->assertSame(InviteType::CREATE, $invite->type);
        $this->assertSame($email, $invite->email);
        $this->assertSame('John Doe', $invite->name);
        $this->assertNull($invite->user_id);
        $this->assertSame($admin->id, $invite->invited_by_id);
        $this->assertSame(InviteStatus::PENDING, $invite->status);
        $this->assertNotNull($invite->sent_at);
        $this->assertTrue($invite->expires_at->isFuture());

        Notification::assertSentOnDemand(Invited::class, function (Invited $notification, array $channels, AnonymousNotifiable $notifiable) use ($email, $invite): bool {
            $token = str($notification->url)->afterLast('/')->toString();

            return $notifiable->routes['mail'] === $email
                && $notification->invite->is($invite)
                && Invite::findByToken($token)?->is($invite) === true;
        });
    }

    public function test_it_creates_a_claim_invite_for_an_existing_profile(): void
    {
        Notification::fake();

        $profile = User::factory()->createQuietly(['password' => null]);
        $email = $this->faker->unique()->safeEmail();

        $invite = SendInvite::handle(InviteType::CLAIM, $email, user: $profile);

        $this->assertSame(InviteType::CLAIM, $invite->type);
        $this->assertSame($profile->id, $invite->user_id);
        $this->assertSame($profile->name, $invite->name);

        Notification::assertSentOnDemandTimes(Invited::class, 1);
    }

    public function test_a_claim_invite_may_reuse_the_profiles_own_email(): void
    {
        Notification::fake();

        $profile = User::factory()->createQuietly(['password' => null]);

        $invite = SendInvite::handle(InviteType::CLAIM, $profile->email, user: $profile);

        $this->assertSame($profile->email, $invite->email);
    }

    public function test_it_revokes_outstanding_invites_for_the_same_recipient(): void
    {
        Notification::fake();

        $email = $this->faker->unique()->safeEmail();
        $previous = Invite::factory()->createQuietly(['email' => $email]);

        SendInvite::handle(InviteType::CREATE, $email);

        $this->assertSame(InviteStatus::REVOKED, $previous->refresh()->status);
        $this->assertSame(1, Invite::query()->pending()->where('email', $email)->count());
    }

    public function test_it_revokes_outstanding_invites_for_the_same_profile(): void
    {
        Notification::fake();

        $profile = User::factory()->createQuietly(['password' => null]);
        $previous = Invite::factory()->claim($profile)->createQuietly();

        SendInvite::handle(InviteType::CLAIM, $this->faker->unique()->safeEmail(), user: $profile);

        $this->assertSame(InviteStatus::REVOKED, $previous->refresh()->status);
    }

    public function test_it_rejects_a_create_invite_for_an_existing_email(): void
    {
        Notification::fake();

        $existing = User::factory()->createQuietly();

        $this->expectException(ValidationException::class);

        try {
            SendInvite::handle(InviteType::CREATE, $existing->email);
        } finally {
            Notification::assertNothingSent();
        }
    }

    public function test_it_rejects_a_claim_invite_using_another_users_email(): void
    {
        Notification::fake();

        $profile = User::factory()->createQuietly(['password' => null]);
        $other = User::factory()->createQuietly();

        $this->expectException(ValidationException::class);

        SendInvite::handle(InviteType::CLAIM, $other->email, user: $profile);
    }

    public function test_it_requires_a_profile_for_a_claim_invite(): void
    {
        Notification::fake();

        $this->expectException(ValidationException::class);

        SendInvite::handle(InviteType::CLAIM, $this->faker->unique()->safeEmail());
    }
}
