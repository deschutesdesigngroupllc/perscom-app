<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Support\Facades\App;

class SubscriptionItemPolicy
{
    public function before(): bool
    {
        return (bool) App::isAdmin();
    }
}
