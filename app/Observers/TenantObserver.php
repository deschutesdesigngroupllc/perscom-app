<?php

namespace App\Observers;

use App\Models\Admin;
use App\Models\Tenant;
use App\Notifications\Admin\NewTenant;
use App\Notifications\Admin\TenantDeleted;
use Illuminate\Support\Facades\Notification;

class TenantObserver
{
    public function created(Tenant $tenant): void
    {
        Notification::send(Admin::all(), new NewTenant($tenant));
    }

    public function deleted(Tenant $tenant): void
    {
        Notification::send(Admin::all(), new TenantDeleted($tenant->name, $tenant->email));
    }
}
