<?php

namespace App\Observers;

use App\Models\Settings;
use Illuminate\Support\Facades\Cache;

class SettingsObserver
{
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

    public function deleted(Settings $settings): void
    {
        Cache::forget($settings->key);
    }
}
