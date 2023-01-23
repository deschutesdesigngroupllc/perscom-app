<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class StatusRecord extends MorphPivot
{
    use HasFactory;

    /**
     * Boot
     */
    public static function boot()
    {
        parent::boot();

        static::created(function (StatusRecord $record) {
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
        return $this->belongsTo(Status::class);
    }
}
