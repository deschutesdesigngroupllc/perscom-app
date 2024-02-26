<?php

namespace Feature\Tenant\Policies;

use App\Models\User;
use Tests\Feature\Tenant\TenantTestCase;

class UserPolicyTest extends TenantTestCase
{
    public function test_cannot_delete_user_who_shares_the_same_email_as_the_tenant_account()
    {
        $user = User::factory()->state([
            'email' => $this->tenant->email,
        ])->create();

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->assertFalse($admin->can('delete', $user));
    }
}
