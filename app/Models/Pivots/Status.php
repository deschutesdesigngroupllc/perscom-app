<?php

namespace App\Models\Pivots;

use App\Models\Status as StatusModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class Status extends MorphPivot
{
    use HasFactory;

    /**
     * Boot
     */
    public static function boot()
    {
        parent::boot();

        static::created(function (Status $record) {
            if ($record->model && $record->model instanceof User) {
                $record->model->status_id = $record->status?->id;
                $record->model->save();
            }
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function model()
    {
        return $this->morphTo('model');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo(StatusModel::class);
    }
}
