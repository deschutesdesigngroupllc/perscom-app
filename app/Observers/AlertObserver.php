<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Admin;
use App\Models\Alert;
use App\Models\Enums\AlertChannel;
use App\Models\Tenant;
use App\Notifications\Tenant\NewAlert;
use Illuminate\Support\Facades\Notification;

class AlertObserver
{
    public function created(Alert $alert): void
    {
        Notification::send(Tenant::all(), new NewAlert($alert));

        if (collect($alert->channels)->contains(AlertChannel::SLACK)) {
            Notification::sendNow(Admin::first(), new NewAlert($alert), ['slack']);
        }
    }
}
