<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Support\Facades\App;

class AlertPolicy
{
    public function before(): bool
    {
        return (bool) App::isAdmin();
    }
}
