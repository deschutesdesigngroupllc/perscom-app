<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    public function register(): void
    {
        $this->hideSensitiveRequestDetails();

        $isDevEnvironment = $this->app->environment('local', 'staging');

        Telescope::filter(function (IncomingEntry $entry) use ($isDevEnvironment) {
            return
                $isDevEnvironment ||
                $entry->isReportableException() ||
                $entry->isFailedRequest() ||
                $entry->isFailedJob() ||
                $entry->isScheduledTask() ||
                $entry->hasMonitoredTag();
        });
    }

    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local', 'staging')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    protected function gate(): void
    {
        Gate::define('viewTelescope', function (Admin|User|null $user = null) {
            return $user instanceof Admin;
        });
    }
}
