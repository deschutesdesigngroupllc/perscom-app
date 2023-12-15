<?php

namespace Perscom\DocumentViewerTool;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class ToolServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            Nova::script('document-viewer-tool', __DIR__.'/../dist/js/tool.js');
            Nova::style('document-viewer-tool', __DIR__.'/../dist/css/tool.css');
        });
    }

    protected function routes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova'])
            ->prefix('nova-vendor/document-viewer-tool')
            ->group(__DIR__.'/../routes/api.php');
    }
}
