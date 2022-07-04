<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

use App\Models\Person;
use App\Models\Status;
use HaydenPierce\ClassFinder\ClassFinder;
use Mako\CustomTableCard\CustomTableCard;


Route::get('/test', function () {
	tenancy()->initialize(\App\Models\Tenant::first());
	$test = tenant()->run(function () {
		return Person::query()->whereHas('statuses')->get()->mapWithKeys(function ($person, $key) {
			return [$person->status->name => $person->status->id];
		})->toArray();
	});
	return $test;
});