<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingRecordCredential newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingRecordCredential newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingRecordCredential query()
 *
 * @mixin \Eloquent
 */
class TrainingRecordCredential extends Pivot
{
    protected $table = 'records_training_credentials';
}
