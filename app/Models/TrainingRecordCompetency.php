<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingRecordCompetency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingRecordCompetency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingRecordCompetency query()
 *
 * @mixin \Eloquent
 */
class TrainingRecordCompetency extends Pivot
{
    protected $table = 'records_training_competencies';
}
