<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\ServiceRecordResource\Pages;

use App\Filament\App\Resources\ServiceRecordResource;
use App\Models\ServiceRecord;
use App\Traits\Filament\InteractsWithBatchRecords;
use App\Traits\Filament\InteractsWithModelNotifications;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class CreateServiceRecord extends CreateRecord
{
    use InteractsWithBatchRecords;
    use InteractsWithModelNotifications;

    protected static string $resource = ServiceRecordResource::class;

    public function mount(): void
    {
        parent::mount();

        $this->form->fill([
            'user_id' => Arr::wrap(request()->query('user_id')),
        ]);
    }

    protected function handleRecordCreation(array $data): Model
    {
        $notificationData = data_get($data, 'model_notifications') ?? [];

        $models = $this->performModelCreations(data_forget($data, 'model_notifications'), function (ServiceRecord $record) use ($notificationData): void {
            $this->performModelNotificationInserts($record, $notificationData);
        });

        return $models->first();
    }
}
