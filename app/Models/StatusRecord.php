<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\StatusRecordObserver;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasStatus;
use Eloquent;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\StatusRecord
 *
 * @property int $id
 * @property int $status_id
 * @property string $model_type
 * @property int $model_id
 * @property string|null $text
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|Eloquent $model
 * @property-read Status $status
 *
 * @method static \Database\Factories\StatusRecordFactory factory($count = null, $state = [])
 * @method static Builder<static>|StatusRecord newModelQuery()
 * @method static Builder<static>|StatusRecord newQuery()
 * @method static Builder<static>|StatusRecord query()
 * @method static Builder<static>|StatusRecord status(\App\Models\Status $status)
 * @method static Builder<static>|StatusRecord whereCreatedAt($value)
 * @method static Builder<static>|StatusRecord whereId($value)
 * @method static Builder<static>|StatusRecord whereModelId($value)
 * @method static Builder<static>|StatusRecord whereModelType($value)
 * @method static Builder<static>|StatusRecord whereStatusId($value)
 * @method static Builder<static>|StatusRecord whereText($value)
 * @method static Builder<static>|StatusRecord whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
#[ObservedBy(StatusRecordObserver::class)]
class StatusRecord extends MorphPivot
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasFactory;
    use HasStatus;

    protected $table = 'model_has_statuses';

    protected $fillable = [
        'text',
    ];

    public function model(): MorphTo
    {
        return $this->morphTo('model');
    }
}
