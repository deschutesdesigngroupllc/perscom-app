<?php

namespace Perscom\Newsfeed;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class CardServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            Nova::script('newsfeed', __DIR__.'/../dist/js/card.js');
            Nova::style('newsfeed', __DIR__.'/../dist/css/card.css');
        });
    }

    protected function routes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova'])
            ->prefix('nova-vendor/newsfeed')
            ->group(__DIR__.'/../routes/api.php');
    }
}
