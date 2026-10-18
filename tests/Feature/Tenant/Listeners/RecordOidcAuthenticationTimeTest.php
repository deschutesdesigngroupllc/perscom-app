<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Listeners;

use App\Listeners\RecordOidcAuthenticationTime;
use App\Models\User;
use App\Support\Passport\OidcAuthorization;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Session;
use ReflectionProperty;
use Tests\Feature\Tenant\TenantTestCase;

class RecordOidcAuthenticationTimeTest extends TenantTestCase
{
    public function test_it_records_the_time_the_user_authenticated(): void
    {
        Date::setTestNow('2026-08-22 10:00:00');

        $user = User::factory()->createQuietly();

        (new RecordOidcAuthenticationTime)->handle(new Login('web', $user, false));

        $this->assertSame(Date::now()->getTimestamp(), Session::get(OidcAuthorization::AUTH_TIME_SESSION_KEY));
        $this->assertSame(Date::now()->getTimestamp(), OidcAuthorization::authTime());

        Date::setTestNow();
    }

    public function test_it_records_the_authentication_time_when_the_user_logs_in(): void
    {
        $user = User::factory()->createQuietly();

        Auth::guard('web')->login($user);

        $this->assertIsInt(OidcAuthorization::authTime());
    }

    public function test_it_clears_the_authentication_time_for_logins_restored_from_a_remember_cookie(): void
    {
        Session::put(OidcAuthorization::AUTH_TIME_SESSION_KEY, now()->subWeek()->getTimestamp());

        $user = User::factory()->createQuietly();

        $guard = Auth::guard('web');

        new ReflectionProperty($guard, 'viaRemember')->setValue($guard, true);

        (new RecordOidcAuthenticationTime)->handle(new Login('web', $user, true));

        $this->assertNull(OidcAuthorization::authTime());
    }

    public function test_it_ignores_logins_on_other_guards(): void
    {
        $user = User::factory()->createQuietly();

        (new RecordOidcAuthenticationTime)->handle(new Login('jwt', $user, false));

        $this->assertNull(OidcAuthorization::authTime());
    }
}
