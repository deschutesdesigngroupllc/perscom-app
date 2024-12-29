<?php

declare(strict_types=1);

namespace App\Observers;

use App\Metrics\Metric;
use App\Metrics\TenantCreationMetric;
use App\Models\Admin;
use App\Models\Tenant;
use App\Notifications\Admin\TenantCreated;
use App\Notifications\Admin\TenantDeleted;
use Illuminate\Support\Facades\Notification;

class TenantObserver
{
    public function created(Tenant $tenant): void
    {
        Notification::send(Admin::all(), new TenantCreated($tenant));

        Metric::increment(TenantCreationMetric::class);
    }

    public function deleted(Tenant $tenant): void
    {
        Notification::send(Admin::all(), new TenantDeleted($tenant->name, $tenant->email));
    }
}
