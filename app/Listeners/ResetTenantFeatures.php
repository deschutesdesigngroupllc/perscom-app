<?php

namespace App\Listeners;

use App\Models\Feature;
use App\Models\Tenant;

class ResetTenantFeatures
{
    public function handle(mixed $event): void
    {
        if (property_exists($event, 'billable') && $event->billable instanceof Tenant) {
            Feature::query()->tenant($event->billable)->delete();
        }
    }
}
