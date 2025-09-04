<?php

declare(strict_types=1);

namespace App\Contracts;

use Eloquent;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @mixin Eloquent
 */
interface SendsModelNotifications
{
    public function modelNotifications(): MorphMany;
}
