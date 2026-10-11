<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Invites;

use App\Actions\Invites\DeliverInvite;
use App\Models\Invite;
use App\Notifications\User\Invited;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Tenant\TenantTestCase;

class DeliverInviteTest extends TenantTestCase
{
    public function test_it_rotates_the_token_and_extends_the_expiration(): void
    {
        Notification::fake();

        $invite = Invite::factory()->withToken('old-token')->expired()->createQuietly();

        DeliverInvite::handle($invite);

        $this->assertNull(Invite::findByToken('old-token'));
        $this->assertTrue($invite->refresh()->expires_at->isFuture());
        $this->assertTrue($invite->isPending());

        Notification::assertSentOnDemand(Invited::class, function (Invited $notification) use ($invite): bool {
            $token = str($notification->url)->afterLast('/')->toString();

            return $notification->url === route('app.invites.accept', ['token' => $token])
                && Invite::findByToken($token)?->is($invite) === true;
        });
    }

    public function test_the_invited_mail_contains_the_acceptance_link(): void
    {
        Notification::fake();

        $invite = Invite::factory()->createQuietly();

        DeliverInvite::handle($invite);

        Notification::assertSentOnDemand(Invited::class, function (Invited $notification) use ($invite): bool {
            $mail = $notification->toMail(new AnonymousNotifiable);
            $mail->assertTo($invite->email);
            $mail->assertSeeInHtml($notification->url, false);

            return true;
        });
    }
}
