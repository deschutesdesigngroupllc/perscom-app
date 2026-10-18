<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int|null $training_record_id
 * @property int|null $competency_id
 *
 * @method static Builder<static>|TrainingRecordCompetency newModelQuery()
 * @method static Builder<static>|TrainingRecordCompetency newQuery()
 * @method static Builder<static>|TrainingRecordCompetency query()
 * @method static Builder<static>|TrainingRecordCompetency whereCompetencyId($value)
 * @method static Builder<static>|TrainingRecordCompetency whereTrainingRecordId($value)
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
class TrainingRecordCompetency extends Pivot
{
    protected $table = 'records_trainings_competencies';
}
