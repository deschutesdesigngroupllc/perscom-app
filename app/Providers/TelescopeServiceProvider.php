<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    public function register(): void
    {
        $this->hideSensitiveRequestDetails();

        Telescope::filter(function (IncomingEntry $entry) {
            if ($this->app->environment('local', 'staging')) {
                return true;
            }

            if ($this->isHealthCheckCommand($entry)) {
                return false;
            }

            return
                $entry->isReportableException() ||
                $entry->isFailedRequest() ||
                $entry->isFailedJob() ||
                $entry->isScheduledTask() ||
                $entry->hasMonitoredTag();
        });
    }

    protected function isHealthCheckCommand(IncomingEntry $entry): bool
    {
        return Str::contains(data_get($entry->content, 'command'), [
            'health:check',
            'health:queue-check-heartbeat',
            'health:schedule-check-heartbeat',
        ]);
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
