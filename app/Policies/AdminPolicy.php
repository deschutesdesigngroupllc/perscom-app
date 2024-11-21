<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Support\Facades\App;

class AdminPolicy
{
    public function before(): bool
    {
        if (App::isAdmin()) {
            return true;
        }

        return false;
    }
}
