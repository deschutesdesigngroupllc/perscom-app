<?php

namespace App\Models\Records;

use App\Models\Status as StatusModel;
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
            if ($record->user) {
                $record->user->status_id = $record->status?->id;
                $record->user->save();
            }
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
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
