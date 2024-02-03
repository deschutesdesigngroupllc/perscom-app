<?php

use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
use UniSharp\LaravelFilemanager\Lfm;
use UniSharp\LaravelFilemanager\Middlewares\CreateDefaultFolder;
use UniSharp\LaravelFilemanager\Middlewares\MultiUser;

Route::group(['middleware' => [InitializeTenancyByDomainOrSubdomain::class, CreateDefaultFolder::class, MultiUser::class, 'web', 'auth']], function () {
    Lfm::routes();
});
