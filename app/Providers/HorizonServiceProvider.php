<?php

namespace App\Providers;

use App\Models\Admin;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    protected function gate(): void
    {
        Gate::define('viewHorizon', function (Admin $admin) {
            return Str::endsWith($admin->email, '@deschutesdesigngroup.com');
        });
    }
}
