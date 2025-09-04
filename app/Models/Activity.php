<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ActivityFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity as BaseActivity;

/**
 * @property int $id
 * @property string|null $log_name
 * @property array<array-key, mixed> $description
 * @property string|null $subject_type
 * @property string|null $event
 * @property int|null $subject_id
 * @property string|null $causer_type
 * @property string|null $causer_id
 * @property Collection<array-key, mixed>|null $properties
 * @property string|null $batch_uuid
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|null $causer
 * @property-read Collection $changes
 * @property-read Model|null $subject
 *
 * @method static Builder<static>|Activity causedBy(Model $causer)
 * @method static ActivityFactory factory($count = null, $state = [])
 * @method static Builder<static>|Activity forBatch(string $batchUuid)
 * @method static Builder<static>|Activity forEvent(string $event)
 * @method static Builder<static>|Activity forSubject(Model $subject)
 * @method static Builder<static>|Activity hasBatch()
 * @method static Builder<static>|Activity inLog(...$logNames)
 * @method static Builder<static>|Activity newModelQuery()
 * @method static Builder<static>|Activity newQuery()
 * @method static Builder<static>|Activity query()
 * @method static Builder<static>|Activity whereBatchUuid($value)
 * @method static Builder<static>|Activity whereCauserId($value)
 * @method static Builder<static>|Activity whereCauserType($value)
 * @method static Builder<static>|Activity whereCreatedAt($value)
 * @method static Builder<static>|Activity whereDescription($value)
 * @method static Builder<static>|Activity whereEvent($value)
 * @method static Builder<static>|Activity whereId($value)
 * @method static Builder<static>|Activity whereLogName($value)
 * @method static Builder<static>|Activity whereProperties($value)
 * @method static Builder<static>|Activity whereSubjectId($value)
 * @method static Builder<static>|Activity whereSubjectType($value)
 * @method static Builder<static>|Activity whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Activity extends BaseActivity
{
    use HasFactory;

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), parent::getCasts(), [
            'description' => 'array',
        ]);
    }
}
