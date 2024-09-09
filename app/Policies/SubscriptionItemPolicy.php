<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Support\Facades\App;

class SubscriptionItemPolicy extends Policy
{
    public function before(): bool
    {
        if (App::isAdmin()) {
            return true;
        }

        return false;
    }
}
