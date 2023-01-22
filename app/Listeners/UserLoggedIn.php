<?php

namespace App\Listeners;

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
        if (tenant()) {
            tenant()->update([
                'last_login_at' => now(),
            ]);
        }
    }
}
