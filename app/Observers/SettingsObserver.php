<?php

namespace App\Observers;

use App\Models\Settings;
use App\Notifications\System\DomainUpdated;
use Illuminate\Support\Facades\Notification;

class SettingsObserver
{
    /**
     * Handle the Settings "created" event.
     *
     * @param  \App\Models\Settings  $settings
     * @return void
     */
    public function created(Settings $settings)
    {
        //
    }

    /**
     * Handle the Settings "updated" event.
     *
     * @param  \App\Models\Settings  $settings
     * @return void
     */
    public function updated(Settings $settings)
    {
        if ($settings->key === 'organization') {
            tenant()->update([
                'name' => $settings->value,
            ]);
        }

        if ($settings->key === 'email') {
            tenant()->update([
                'email' => $settings->value,
            ]);
        }

        if ($settings->key === 'subdomain') {
            tenant()->domain->update([
                'domain' => $settings->value,
            ]);

            Notification::send(tenant(), new DomainUpdated(tenant()));
        }
    }

    /**
     * Handle the Settings "deleted" event.
     *
     * @param  \App\Models\Settings  $settings
     * @return void
     */
    public function deleted(Settings $settings)
    {
        //
    }

    /**
     * Handle the Settings "restored" event.
     *
     * @param  \App\Models\Settings  $settings
     * @return void
     */
    public function restored(Settings $settings)
    {
        //
    }

    /**
     * Handle the Settings "force deleted" event.
     *
     * @param  \App\Models\Settings  $settings
     * @return void
     */
    public function forceDeleted(Settings $settings)
    {
        //
    }
}
