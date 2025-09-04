<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @method static Builder<static>|TrainingRecordCredential newModelQuery()
 * @method static Builder<static>|TrainingRecordCredential newQuery()
 * @method static Builder<static>|TrainingRecordCredential query()
 *
 * @mixin \Eloquent
 */
class TrainingRecordCredential extends Pivot
{
    protected $table = 'records_training_credentials';
}
