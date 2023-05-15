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
        optional(tenant(), static function (Tenant $tenant) {
            $tenant->updateQuietly([
                'last_login_at' => now(),
            ]);
        });
    }
}
