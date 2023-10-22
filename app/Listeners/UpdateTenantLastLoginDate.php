<?php

namespace App\Listeners;

use App\Models\Tenant;
use Illuminate\Auth\Events\Login;

class UpdateTenantLastLoginDate
{
    public function handle(Login $event): void
    {
        optional(tenant(), static function (Tenant $tenant) {
            $tenant->updateQuietly([
                'last_login_at' => now(),
            ]);
        });
    }
}
