<?php

namespace App\Listeners;

use App\Models\Tenant;
use Illuminate\Auth\Events\Login;

class UserLoggedIn
{
    /**
     * Handle the event.
     *
     * @param  object  $event
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
