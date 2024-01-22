<?php

namespace Tests\Feature\Central\Jobs\Tenant;

use App\Models\Tenant;
use App\Models\User;
use Tests\Feature\Central\CentralTestCase;

class CreateInitialTenantUserTest extends CentralTestCase
{
    public function test_initial_user_is_created()
    {
        $tenant = Tenant::factory()->create();
        $tenant->run(function (Tenant $tenant) {
            $this->assertDatabaseHas('users', [
                'name' => 'Admin',
            ]);

            $admin = User::oldest()->first();

            $this->assertContains('Admin', $admin->roles->pluck('name'));
        });
    }
}
