<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Listeners;

use App\Listeners\UpdateTenantLastLoginDate;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Carbon;
use Tests\Feature\Tenant\TenantTestCase;

class UpdateTenantLastLoginDateTest extends TenantTestCase
{
    public function test_handle_stamps_the_current_tenant_last_login_at(): void
    {
        Carbon::setTestNow('2026-08-22 10:00:00');

        $user = User::factory()->createQuietly();

        (new UpdateTenantLastLoginDate)->handle(new Login('web', $user, false));

        $this->assertNotNull($this->tenant->refresh()->last_login_at);
        $this->assertSame(
            Carbon::now()->toDateTimeString(),
            Carbon::parse($this->tenant->last_login_at)->toDateTimeString(),
        );

        Carbon::setTestNow();
    }
}
