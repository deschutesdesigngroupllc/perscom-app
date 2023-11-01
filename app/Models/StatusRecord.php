<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

/**
 * App\Models\StatusRecord
 *
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $model
 * @property-read \App\Models\Status|null $status
 *
 * @method static \Database\Factories\StatusRecordFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|StatusRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StatusRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StatusRecord query()
 *
 * @mixin \Eloquent
 */
class StatusRecord extends MorphPivot
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $with = ['status'];

    /**
     * @var string
     */
    protected $table = 'model_has_statuses';

    public static function boot(): void
    {
        parent::boot();

        static::created(function (StatusRecord $record) {
            if ($record->model instanceof User) {
                $record->model->status_id = $record->status?->id;
                $record->model->save();
            }
        });
    }

    public function model(): BelongsTo
    {
        return $this->morphTo('model');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }
}
