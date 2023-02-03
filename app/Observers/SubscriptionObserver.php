<?php

namespace App\Observers;

use App\Models\Admin;
use App\Notifications\Admin\NewSubscription;
use Illuminate\Support\Facades\Notification;
use Laravel\Cashier\Subscription;

class SubscriptionObserver
{
    /**
     * Handle the Subscription "created" event.
     *
     * @param  \Laravel\Cashier\Subscription  $subscription
     * @return void
     */
    public function created(Subscription $subscription)
    {
        Notification::send(Admin::all(), new NewSubscription($subscription));
    }

    /**
     * Handle the Subscription "updated" event.
     *
     * @param  \Laravel\Cashier\Subscription  $subscription
     * @return void
     */
    public function updated(Subscription $subscription)
    {
        //
    }

    /**
     * Handle the Subscription "deleted" event.
     *
     * @param  \Laravel\Cashier\Subscription  $subscription
     * @return void
     */
    public function deleted(Subscription $subscription)
    {
        //
    }

    /**
     * Handle the Subscription "restored" event.
     *
     * @param  \Laravel\Cashier\Subscription  $subscription
     * @return void
     */
    public function restored(Subscription $subscription)
    {
        //
    }

    /**
     * Handle the Subscription "force deleted" event.
     *
     * @param  \Laravel\Cashier\Subscription  $subscription
     * @return void
     */
    public function forceDeleted(Subscription $subscription)
    {
        //
    }
}
