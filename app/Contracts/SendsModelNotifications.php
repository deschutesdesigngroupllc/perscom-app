<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\ModelNotification;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @mixin Eloquent
 *
 * @template T of ModelNotification
 */
interface SendsModelNotifications
{
    /**
     * @return MorphMany<ModelNotification, T>
     */
    public function modelNotifications(): MorphMany;
}
