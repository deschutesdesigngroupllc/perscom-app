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
     * @return void
     */
    public function created(Subscription $subscription)
    {
        Notification::send(Admin::all(), new NewSubscription($subscription));
    }
}
