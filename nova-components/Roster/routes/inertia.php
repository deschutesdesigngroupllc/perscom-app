<?php

use Illuminate\Support\Facades\Route;
use Laravel\Nova\Http\Requests\NovaRequest;
use Perscom\Roster\Roster;

/*
|--------------------------------------------------------------------------
| Tool Routes
|--------------------------------------------------------------------------
|
| Here is where you may register Inertia routes for your tool. These are
| loaded by the ServiceProvider of the tool. The routes are protected
| by your tool's "Authorize" middleware by default. Now - go build!
|
*/

Route::get('/', function (NovaRequest $request) {
    return inertia('Roster', [
        'jwt' => Roster::generateJwt(),
        'tenant_id' => tenant()->getTenantKey(),
        'widget_url' => env('WIDGET_URL'),
    ]);
});
