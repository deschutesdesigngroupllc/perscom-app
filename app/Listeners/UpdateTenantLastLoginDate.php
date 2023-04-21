<?php

namespace App\Listeners;

use App\Models\Tenant;
use Illuminate\Auth\Events\Login;

class UpdateTenantLastLoginDate
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(Login $event)
    {
        optional(tenant(), function (Tenant $tenant) {
            $tenant->update([
                'last_login_at' => now(),
            ]);
        });
    }
}
