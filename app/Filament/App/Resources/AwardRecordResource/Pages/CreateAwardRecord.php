<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AwardRecordResource\Pages;

use App\Filament\App\Resources\AwardRecordResource;
use App\Models\ServiceRecord;
use App\Traits\Filament\InteractsWithBatchRecords;
use App\Traits\Filament\InteractsWithModelNotifications;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAwardRecord extends CreateRecord
{
    use InteractsWithBatchRecords;
    use InteractsWithModelNotifications;

    protected static string $resource = AwardRecordResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $notificationData = data_get($data, 'model_notifications') ?? [];

        $models = $this->performModelCreations(data_forget($data, 'model_notifications'), function (ServiceRecord $record) use ($notificationData) {
            $this->performModelNotificationInserts($record, $notificationData);
        });

        return $models->first();
    }
}
