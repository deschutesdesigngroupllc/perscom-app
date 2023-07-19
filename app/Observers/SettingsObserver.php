<?php

namespace App\Observers;

use App\Models\Settings;
use Illuminate\Support\Facades\Cache;

class SettingsObserver
{
    /**
     * Handle the Settings "created" event.
     *
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
     * @return void
     */
    public function updated(Settings $settings)
    {
        Cache::forget($settings->key);

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
     * @return void
     */
    public function deleted(Settings $settings)
    {
        Cache::forget($settings->key);
    }
}
