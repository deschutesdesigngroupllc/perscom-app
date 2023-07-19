<?php

namespace Tests\Feature\Tenant\Jobs;

use App\Mail\Tenant\NewTenantMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Tests\Feature\Tenant\TenantTestCase;

class CreateInitialTenantUserTest extends TenantTestCase
{
    protected $fakeMail = true;

    public function test_initial_user_is_created()
    {
        $this->assertDatabaseHas('users', [
            'name' => 'Admin',
        ]);

        $admin = User::oldest()->first();

        $this->assertContains('Admin', $admin->roles->pluck('name'));
    }

    public function test_initial_user_mail_is_sent()
    {
        Mail::assertQueued(NewTenantMail::class);
    }
}
