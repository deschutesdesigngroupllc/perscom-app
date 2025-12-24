<?php

declare(strict_types=1);

$providers = [
    App\Providers\AppServiceProvider::class,
    App\Providers\ContextServiceProvider::class,
    App\Providers\Filament\AppPanelProvider::class,
    App\Providers\HealthServiceProvider::class,
    App\Providers\HorizonServiceProvider::class,
    App\Providers\RouteServiceProvider::class,
    App\Providers\SparkServiceProvider::class,
    App\Providers\TelescopeServiceProvider::class,
    App\Providers\TwigServiceProvider::class,
];

if (config('tenancy.enabled')) {
    $providers[] = App\Providers\TenancyServiceProvider::class;
    $providers[] = App\Providers\Filament\AdminPanelProvider::class;
}

return $providers;
