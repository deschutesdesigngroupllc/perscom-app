<?php

namespace App\Observers;

use App\Models\ServiceRecord;
use App\Notifications\Tenant\NewServiceRecord;
use Illuminate\Support\Facades\Notification;

class ServiceRecordObserver
{
    /**
     * Handle the Service "created" event.
     *
     * @return void
     */
    public function created(ServiceRecord $service)
    {
        Notification::send($service->user, new NewServiceRecord($service));
    }

    /**
     * Handle the Service "updated" event.
     *
     * @return void
     */
    public function updated(ServiceRecord $service)
    {
        //
    }

    /**
     * Handle the Service "deleted" event.
     *
     * @return void
     */
    public function deleted(ServiceRecord $service)
    {
        //
    }

    /**
     * Handle the Service "restored" event.
     *
     * @return void
     */
    public function restored(ServiceRecord $service)
    {
        //
    }

    /**
     * Handle the Service "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(ServiceRecord $service)
    {
        //
    }
}
