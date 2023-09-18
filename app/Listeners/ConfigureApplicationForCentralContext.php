<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Config;

class ConfigureApplicationForCentralContext
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        Config::set('mail.from.name', env('MAIL_FROM_NAME'));
        Config::set('app.timezone', 'UTC');
        date_default_timezone_set('UTC');
    }
}
