<?php

namespace App\Observers;

use App\Models\StatusRecord;
use App\Models\User;

class StatusRecordObserver
{
    public function created(StatusRecord $statusRecord): void
    {
        if ($statusRecord->model instanceof User) {
            $statusRecord->model->status_id = $statusRecord->status?->id;
            $statusRecord->model->save();
        }
    }

    public function updated(StatusRecord $statusRecord): void
    {
        //
    }

    public function deleted(StatusRecord $statusRecord): void
    {
        //
    }

    public function restored(StatusRecord $statusRecord): void
    {
        //
    }

    public function forceDeleted(StatusRecord $statusRecord): void
    {
        //
    }
}
