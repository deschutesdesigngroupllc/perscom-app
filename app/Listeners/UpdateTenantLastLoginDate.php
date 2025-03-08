<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\Tenant;
use Illuminate\Auth\Events\Login;

class UpdateTenantLastLoginDate
{
    public function handle(Login $event): void
    {
        optional(tenant(), static function (Tenant $tenant): void {
            $tenant->updateQuietly([
                'last_login_at' => now(),
            ]);
        });
    }
}
