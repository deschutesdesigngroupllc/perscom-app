<?php

namespace Tests\Feature\Tenant\Jobs;

use App\Models\User;
use Tests\Feature\Tenant\TenantTestCase;

class CreateInitialTenantUserTest extends TenantTestCase
{
    public function test_initial_user_is_created()
    {
        $this->assertDatabaseHas('users', [
            'name' => 'Admin',
        ]);

        $admin = User::oldest()->first();

        $this->assertContains('Admin', $admin->roles->pluck('name'));
    }
}
