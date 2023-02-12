<?php

namespace App\Observers;

use App\Models\Settings;

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
            tenant()->domains()->updateOrCreate([
                'is_custom_subdomain' => true,
            ], [
                'domain' => $settings->value,
            ]);
        }
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
            tenant()->domains()->updateOrCreate([
                'is_custom_subdomain' => true,
            ], [
                'domain' => $settings->value,
            ]);
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
