<?php

namespace App\Observers;

use App\Models\Settings;
use Illuminate\Support\Facades\Cache;

class SettingsObserver
{
    /**
     * Handle the Settings "created" event.
     */
    public function created(Settings $settings): void
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
     */
    public function updated(Settings $settings): void
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
     */
    public function deleted(Settings $settings): void
    {
        Cache::forget($settings->key);
    }

    /**
     * Handle the Settings "restored" event.
     */
    public function restored(Settings $settings): void
    {
        //
    }

    /**
     * Handle the Settings "force deleted" event.
     */
    public function forceDeleted(Settings $settings): void
    {
        //
    }
}
