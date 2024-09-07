<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Admin;
use App\Models\Subscription;
use App\Notifications\Admin\NewSubscription;
use Illuminate\Support\Facades\Notification;

class SubscriptionObserver
{
    public function created(Subscription $subscription): void
    {
        Notification::send(Admin::all(), new NewSubscription($subscription));
    }
}
