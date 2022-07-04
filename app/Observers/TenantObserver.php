<?php

namespace App\Observers;

use App\Models\Tenant;
use App\Models\User;
use App\Notifications\Admin\NewTenant;
use App\Notifications\Admin\TenantDeleted;
use Illuminate\Support\Facades\Notification;

class TenantObserver
{
    /**
     * Handle the Tenant "created" event.
     *
     * @param  \App\Models\Tenant  $tenant
     * @return void
     */
    public function created(Tenant $tenant)
    {
	    Notification::sendNow(User::all(), new NewTenant($tenant));
    }

	    /**
     * Handle the Tenant "updated" event.
     *
     * @param  \App\Models\Tenant  $tenant
     * @return void
     */
    public function updated(Tenant $tenant)
    {
        //
    }

    /**
     * Handle the Tenant "deleted" event.
     *
     * @param  \App\Models\Tenant  $tenant
     * @return void
     */
    public function deleted(Tenant $tenant)
    {
	    Notification::sendNow(User::all(), new TenantDeleted($tenant));
    }

    /**
     * Handle the Tenant "restored" event.
     *
     * @param  \App\Models\Tenant  $tenant
     * @return void
     */
    public function restored(Tenant $tenant)
    {
        //
    }

    /**
     * Handle the Tenant "force deleted" event.
     *
     * @param  \App\Models\Tenant  $tenant
     * @return void
     */
    public function forceDeleted(Tenant $tenant)
    {
        //
    }
}
