<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\ModelNotification;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

/**
 * @mixin Eloquent
 *
 * @property Collection $modelNotifications
 */
interface SendsModelNotifications
{
    /**
     * @return MorphMany<ModelNotification>
     */
    public function modelNotifications(): MorphMany;
}
