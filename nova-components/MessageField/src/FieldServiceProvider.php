<?php

namespace Perscom\MessageField;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class FieldServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Nova::serving(function (ServingNova $event) {
            Nova::script('message-field', __DIR__.'/../dist/js/field.js');
            Nova::style('message-field', __DIR__.'/../dist/css/field.css');
        });
    }
}
