<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AssignmentRecordResource\Pages;

use App\Filament\App\Resources\AssignmentRecordResource;
use App\Traits\Filament\InteractsWithBatchRecords;
use App\Traits\Filament\InteractsWithModelNotifications;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAssignmentRecord extends CreateRecord
{
    use InteractsWithBatchRecords {
        InteractsWithBatchRecords::handleRecordCreation as batchCreate;
    }
    use InteractsWithModelNotifications {
        InteractsWithModelNotifications::handleRecordCreation as modelCreate;
    }

    protected static string $resource = AssignmentRecordResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $notificationData = data_get($data, 'model_notifications');

        $model = $this->batchCreate(data_forget($data, 'model_notifications'));

        $this->performModelNotificationInserts($model, $notificationData);

        return $model;
    }
}
