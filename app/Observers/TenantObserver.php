<?php

namespace App\Observers;

use App\Models\Admin;
use App\Models\Tenant;
use App\Notifications\Admin\NewTenant;
use App\Notifications\Admin\TenantDeleted;
use Illuminate\Support\Facades\Notification;

class TenantObserver
{
    /**
     * Handle the Tenant "created" event.
     *
     * @return void
     */
    public function created(Tenant $tenant)
    {
        Notification::send(Admin::all(), new NewTenant($tenant));
    }

    /**
     * Handle the Tenant "deleted" event.
     *
     * @return void
     */
    public function deleted(Tenant $tenant)
    {
        Notification::send(Admin::all(), new TenantDeleted($tenant->name, $tenant->email));
    }
}
