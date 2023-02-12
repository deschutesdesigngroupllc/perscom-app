<?php

namespace App\Providers;

use App\Services\Feature;
use Illuminate\Support\ServiceProvider;

class FeatureServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('feature', function ($app) {
            return new Feature();
        });
    }
}
