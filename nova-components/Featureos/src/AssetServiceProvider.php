<?php

namespace Perscom\Featureos;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Perscom\Roster\Http\Middleware\Authorize;

class AssetServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->booted(function () {
            Route::middleware(['nova', Authorize::class, 'universal'])
                ->prefix('nova-vendor/featureos')
                ->group(__DIR__.'/../routes/api.php');
        });

        Nova::serving(function (ServingNova $event) {
            if (! $event->request->isCentralRequest()) {
                Nova::script('featureos', __DIR__.'/../dist/js/asset.js');
                Nova::style('featureos', __DIR__.'/../dist/css/asset.css');
            }
        });
    }
}
