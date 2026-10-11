<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Models;

use App\Models\Admin;
use App\Models\User;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Tenant\TenantTestCase;

class UserMultiFactorAuthenticationTest extends TenantTestCase
{
    public function test_user_implements_multi_factor_authentication_contracts(): void
    {
        $user = new User;

        $this->assertInstanceOf(HasAppAuthentication::class, $user);
        $this->assertInstanceOf(HasAppAuthenticationRecovery::class, $user);
        $this->assertInstanceOf(HasEmailAuthentication::class, $user);
    }

    public function test_admin_implements_multi_factor_authentication_contracts(): void
    {
        $admin = new Admin;

        $this->assertInstanceOf(HasAppAuthentication::class, $admin);
        $this->assertInstanceOf(HasAppAuthenticationRecovery::class, $admin);
        $this->assertInstanceOf(HasEmailAuthentication::class, $admin);
    }

    public function test_app_authentication_secret_is_encrypted_and_hidden(): void
    {
        $user = User::factory()->createQuietly();

        $user->saveAppAuthenticationSecret('SECRETVALUE123456');

        $user->refresh();

        $this->assertSame('SECRETVALUE123456', $user->getAppAuthenticationSecret());
        $this->assertNotSame('SECRETVALUE123456', DB::connection('tenant')->table('users')->where('id', $user->id)->value('app_authentication_secret'));
        $this->assertArrayNotHasKey('app_authentication_secret', $user->toArray());
    }

    public function test_recovery_codes_are_persisted_and_hidden(): void
    {
        $user = User::factory()->createQuietly();

        $user->saveAppAuthenticationRecoveryCodes(['code-one', 'code-two']);

        $user->refresh();

        $this->assertSame(['code-one', 'code-two'], $user->getAppAuthenticationRecoveryCodes());
        $this->assertArrayNotHasKey('app_authentication_recovery_codes', $user->toArray());
    }

    public function test_email_authentication_can_be_toggled(): void
    {
        $user = User::factory()->createQuietly();

        $this->assertFalse($user->hasEmailAuthentication());

        $user->toggleEmailAuthentication(true);

        $this->assertTrue($user->refresh()->hasEmailAuthentication());
        $this->assertArrayNotHasKey('has_email_authentication', $user->toArray());
    }
}
