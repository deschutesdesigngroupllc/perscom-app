<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Tenant;

use App\Actions\Tenant\ResetTenantSubdomain;
use App\Models\Domain;
use App\Settings\DashboardSettings;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Tenant\TenantTestCase;

class ResetTenantSubdomainTest extends TenantTestCase
{
    public function test_removes_custom_subdomain_and_clears_the_setting(): void
    {
        Notification::fake();

        Domain::factory()->createQuietly([
            'tenant_id' => $this->tenant->getKey(),
            'domain' => 'customorg',
            'is_custom_subdomain' => true,
        ]);

        $settings = resolve(DashboardSettings::class);
        $settings->subdomain = 'customorg';
        $settings->save();

        $deleted = (new ResetTenantSubdomain)->handle($this->tenant);

        $this->assertSame(1, $deleted);
        $this->assertDatabaseMissing('domains', [
            'tenant_id' => $this->tenant->getKey(),
            'domain' => 'customorg',
        ], 'mysql');

        $this->assertNull(resolve(DashboardSettings::class)->subdomain);
    }

    public function test_returns_zero_when_no_custom_subdomain_exists(): void
    {
        Notification::fake();

        $deleted = (new ResetTenantSubdomain)->handle($this->tenant);

        $this->assertSame(0, $deleted);
    }
}
