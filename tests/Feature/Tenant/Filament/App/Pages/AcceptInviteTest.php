<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Pages;

use App\Filament\App\Pages\Auth\AcceptInvite;
use App\Models\Invite;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class AcceptInviteTest extends TenantTestCase
{
    public function test_the_acceptance_page_renders_for_a_valid_token(): void
    {
        $invite = Invite::factory()->withToken('valid-token')->createQuietly();

        $this->get(route('app.invites.accept', ['token' => 'valid-token']))
            ->assertOk()
            ->assertSee('Accept Invitation')
            ->assertSee($invite->email);
    }

    public function test_a_create_invite_registers_and_logs_in_the_user(): void
    {
        Notification::fake();

        $invite = Invite::factory()->withToken('valid-token')->createQuietly(['name' => 'Jane Doe']);

        Livewire::test(AcceptInvite::class, ['token' => 'valid-token'])
            ->assertSchemaStateSet(['email' => $invite->email, 'name' => 'Jane Doe'])
            ->fillForm([
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
            ])
            ->call('accept')
            ->assertHasNoErrors()
            ->assertRedirect(Filament::getUrl());

        $user = User::query()->where('email', $invite->email)->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($invite->refresh()->accepted_at);
    }

    public function test_a_claim_invite_hides_the_name_field_and_claims_the_profile(): void
    {
        Notification::fake();

        $profile = User::factory()->createQuietly(['password' => null]);
        $invite = Invite::factory()->claim($profile)->withToken('claim-token')->createQuietly();

        Livewire::test(AcceptInvite::class, ['token' => 'claim-token'])
            ->assertSee('Claim Your Profile')
            ->assertFormFieldHidden('name')
            ->fillForm([
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
            ])
            ->call('accept')
            ->assertHasNoErrors()
            ->assertRedirect(Filament::getUrl());

        $this->assertAuthenticatedAs($profile->refresh());
        $this->assertSame($invite->email, $profile->email);
    }

    public function test_password_confirmation_must_match(): void
    {
        Invite::factory()->withToken('valid-token')->createQuietly();

        Livewire::test(AcceptInvite::class, ['token' => 'valid-token'])
            ->fillForm([
                'name' => 'Jane Doe',
                'password' => 'Password123!',
                'password_confirmation' => 'Different123!',
            ])
            ->call('accept')
            ->assertHasFormErrors(['password']);

        $this->assertGuest();
    }

    public function test_an_unknown_token_shows_the_invalid_message(): void
    {
        Livewire::test(AcceptInvite::class, ['token' => 'unknown-token'])
            ->assertSee('This invitation is invalid, has expired, or has already been used.')
            ->call('accept')
            ->assertNoRedirect();

        $this->assertGuest();
    }

    public function test_a_claim_invite_for_an_already_claimed_profile_shows_the_invalid_message(): void
    {
        $profile = User::factory()->createQuietly();
        Invite::factory()->claim($profile)->withToken('claim-token')->createQuietly();

        Livewire::test(AcceptInvite::class, ['token' => 'claim-token'])
            ->assertSee('This invitation is invalid, has expired, or has already been used.')
            ->call('accept')
            ->assertNoRedirect();

        $this->assertGuest();
    }

    public function test_an_expired_invite_shows_the_invalid_message(): void
    {
        Invite::factory()->withToken('expired-token')->expired()->createQuietly();

        Livewire::test(AcceptInvite::class, ['token' => 'expired-token'])
            ->assertSee('This invitation is invalid, has expired, or has already been used.');
    }
}
