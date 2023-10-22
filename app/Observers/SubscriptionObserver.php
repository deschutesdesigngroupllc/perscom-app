<?php

namespace App\Observers;

use App\Models\Admin;
use App\Notifications\Admin\NewSubscription;
use Illuminate\Support\Facades\Notification;
use Laravel\Cashier\Subscription;

class SubscriptionObserver
{
    public function created(Subscription $subscription): void
    {
        Notification::send(Admin::all(), new NewSubscription($subscription));
    }
}
