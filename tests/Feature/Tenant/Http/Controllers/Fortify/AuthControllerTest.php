<?php

namespace Tests\Feature\Tenant\Http\Controllers\Fortify;

use Illuminate\Auth\Events\Authenticated;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Requests\VerifyEmailRequest;
use Tests\Feature\Tenant\TenantTestCase;

class AuthControllerTest extends TenantTestCase
{
    public function test_login_page_can_be_reached()
    {
        $this->get(route('login'))
            ->assertInertia(function (AssertableInertia $page) {
                $page->component('auth/Login');
            })->assertSuccessful();
    }

    public function test_can_login()
    {
        Event::fake(Authenticated::class);

        $this->post(route('login'), [
            'email' => $this->user->email,
            'password' => 'password',
        ])
            ->assertRedirect(route('nova.pages.dashboard'))
            ->assertSessionHasNoErrors();

        $this->assertAuthenticatedAs($this->user);

        Event::assertDispatched(Authenticated::class);
    }

    public function test_cannot_login_with_wrong_password()
    {
        Event::fake(Failed::class);

        $this->post(route('login'), [
            'email' => $this->user->email,
            'password' => 'wrong',
        ])
            ->assertRedirect($this->tenant->url)
            ->assertSessionHasErrors('email');

        $this->assertGuest();

        Event::assertDispatched(Failed::class);
    }

    public function test_cannot_login_with_wrong_email()
    {
        Event::fake(Failed::class);

        $this->post(route('login'), [
            'email' => $this->faker->email,
            'password' => 'password',
        ])
            ->assertRedirect($this->tenant->url)
            ->assertSessionHasErrors('email');

        $this->assertGuest();

        Event::assertDispatched(Failed::class);
    }

    public function test_can_logout()
    {
        Event::fake(Logout::class);

        $this->actingAs($this->user);

        $this->post(route('logout'))
            ->assertRedirect($this->tenant->url);

        $this->assertGuest();

        Event::assertDispatched(Logout::class);
    }

    public function test_register_page_can_be_reached()
    {
        $this->get(route('register'))
            ->assertInertia(function (AssertableInertia $page) {
                $page->component('auth/Register');
            })->assertSuccessful();
    }

    public function test_verify_email_page_can_be_reached()
    {
        $this->user->forceFill([
            'email_verified_at' => null,
        ])->save();

        $this->actingAs($this->user);

        $this->get(route('verification.notice'))
            ->assertInertia(function (AssertableInertia $page) {
                $page->component('auth/VerifyEmail');
            })->assertSuccessful();
    }

    public function test_can_send_email_verification_notification()
    {
        Notification::fake();

        $this->user->forceFill([
            'email_verified_at' => null,
        ])->save();

        $this->actingAs($this->user);

        $this->post(route('verification.send'))
            ->assertRedirect()
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status', Fortify::VERIFICATION_LINK_SENT);

        Notification::assertSentTo([$this->user], VerifyEmail::class);
    }

    public function test_verify_email_success_page_can_be_reached()
    {
        Event::fake(Verified::class);

        $this->withoutMiddleware(ValidateSignature::class);

        $request = $this->mock(VerifyEmailRequest::class);
        $request->allows('user')->andReturn($this->user);

        $this->instance(VerifyEmailRequest::class, $request);

        $this->user->forceFill([
            'email_verified_at' => null,
        ])->save();

        $this->actingAs($this->user);

        $this->get(route('verification.verify', [
            'id' => Str::random(),
            'hash' => Str::random(),
        ]))->assertRedirect();

        $this->assertTrue($this->user->hasVerifiedEmail());

        Event::assertDispatched(Verified::class);
    }

    public function test_forgot_password_page_can_be_reached()
    {
        $this->get(route('password.request'))
            ->assertInertia(function (AssertableInertia $page) {
                $page->component('auth/ForgotPassword');
            })->assertSuccessful();
    }

    public function test_can_send_forgot_password_notification_and_reach_reset_password_page()
    {
        Notification::fake();

        $this->post(route('password.email', [
            'email' => $this->user->email,
        ]))
            ->assertRedirect()
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status', Lang::get('passwords.sent'));

        Notification::assertSentTo([$this->user], ResetPassword::class, function (ResetPassword $notification) {
            $this->get(route('password.reset', [
                'token' => $notification->token,
            ]))->assertSuccessful();

            return true;
        });
    }

    public function test_can_send_forgot_password_notification_and_reset_password()
    {
        Notification::fake();
        Event::fake(PasswordReset::class);

        $this->post(route('password.email', [
            'email' => $this->user->email,
        ]))
            ->assertRedirect()
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status', Lang::get('passwords.sent'));

        Notification::assertSentTo([$this->user], ResetPassword::class, function (ResetPassword $notification) {
            $this->post(route('password.update', [
                'token' => $notification->token,
                'email' => $this->user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]))
                ->assertRedirect(route('login'))
                ->assertSessionHasNoErrors()
                ->assertSessionHas('status', Lang::get('passwords.reset'));

            Event::assertDispatched(PasswordReset::class);

            return true;
        });
    }
}
