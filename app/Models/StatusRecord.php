<?php

namespace App\Models;

use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

/**
 * App\Models\StatusRecord
 *
 * @property int $id
 * @property int $status_id
 * @property string $model_type
 * @property int $model_id
 * @property string|null $text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $model
 * @property-read \App\Models\Status $status
 *
 * @method static \Database\Factories\StatusRecordFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|StatusRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StatusRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StatusRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|StatusRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatusRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatusRecord whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatusRecord whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatusRecord whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatusRecord whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatusRecord whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class StatusRecord extends MorphPivot
{
    use ClearsResponseCache;
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'text',
    ];

    /**
     * @var string[]
     */
    protected $with = ['status'];

    /**
     * @var string
     */
    protected $table = 'model_has_statuses';

    public function model(): BelongsTo
    {
        return $this->morphTo('model');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }
}
