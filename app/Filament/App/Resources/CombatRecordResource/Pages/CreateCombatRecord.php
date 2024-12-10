<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\CombatRecordResource\Pages;

use App\Filament\App\Resources\CombatRecordResource;
use App\Models\CombatRecord;
use App\Traits\Filament\InteractsWithBatchRecords;
use App\Traits\Filament\InteractsWithModelNotifications;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateCombatRecord extends CreateRecord
{
    use InteractsWithBatchRecords;
    use InteractsWithModelNotifications;

    protected static string $resource = CombatRecordResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $notificationData = data_get($data, 'model_notifications') ?? [];

        $models = $this->performModelCreations(data_forget($data, 'model_notifications'), function (CombatRecord $record) use ($notificationData) {
            $this->performModelNotificationInserts($record, $notificationData);
        });

        return $models->first();
    }
}
