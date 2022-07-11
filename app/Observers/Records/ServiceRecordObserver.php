<?php

namespace App\Observers\Records;

use App\Models\Records\Service;
use App\Notifications\Records\NewServiceRecord;
use Illuminate\Support\Facades\Notification;

class ServiceRecordObserver
{
    /**
     * Handle the Service "created" event.
     *
     * @param  \App\Models\Records\Service  $service
     * @return void
     */
    public function created(Service $service)
    {
        Notification::send($service->person->users, new NewServiceRecord($service));
    }

    /**
     * Handle the Service "updated" event.
     *
     * @param  \App\Models\Records\Service  $service
     * @return void
     */
    public function updated(Service $service)
    {
        //
    }

    /**
     * Handle the Service "deleted" event.
     *
     * @param  \App\Models\Records\Service  $service
     * @return void
     */
    public function deleted(Service $service)
    {
        //
    }

    /**
     * Handle the Service "restored" event.
     *
     * @param  \App\Models\Records\Service  $service
     * @return void
     */
    public function restored(Service $service)
    {
        //
    }

    /**
     * Handle the Service "force deleted" event.
     *
     * @param  \App\Models\Records\Service  $service
     * @return void
     */
    public function forceDeleted(Service $service)
    {
        //
    }
}
