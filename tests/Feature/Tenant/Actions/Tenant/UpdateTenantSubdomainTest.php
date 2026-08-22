<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Tenant;

use App\Actions\Tenant\UpdateTenantSubdomain;
use App\Rules\SubdomainRule;
use App\Settings\DashboardSettings;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Tenant\TenantTestCase;

class UpdateTenantSubdomainTest extends TenantTestCase
{
    public function test_creates_a_custom_subdomain_and_persists_the_setting(): void
    {
        Notification::fake();

        $result = UpdateTenantSubdomain::handle($this->tenant, 'brandneworg');

        $this->assertTrue($result);
        $this->assertDatabaseHas('domains', [
            'tenant_id' => $this->tenant->getKey(),
            'domain' => 'brandneworg',
            'is_custom_subdomain' => true,
        ], 'mysql');

        $this->assertSame('brandneworg', resolve(DashboardSettings::class)->subdomain);
    }

    public function test_rejects_a_reserved_subdomain(): void
    {
        Notification::fake();

        $reserved = SubdomainRule::$reservedSubdomains[0];

        $result = UpdateTenantSubdomain::handle($this->tenant, $reserved);

        $this->assertFalse($result);
        $this->assertDatabaseMissing('domains', [
            'tenant_id' => $this->tenant->getKey(),
            'domain' => $reserved,
        ], 'mysql');
    }
}
