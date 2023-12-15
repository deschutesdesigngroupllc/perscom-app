<?php

namespace Perscom\HtmlField;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class FieldServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Nova::serving(function (ServingNova $event) {
            Nova::script('HtmlField', __DIR__.'/../dist/js/field.js');
            Nova::style('HtmlField', __DIR__.'/../dist/css/field.css');
        });
    }
}
