<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\StatusRecord;
use App\Models\User;

class StatusRecordObserver
{
    public function created(StatusRecord $statusRecord): void
    {
        if ($statusRecord->model instanceof User) {
            $statusRecord->forceFill([
                'status_id' => $statusRecord->status_id,
            ])->save();
        }
    }
}
