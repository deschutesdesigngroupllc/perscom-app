<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\QualificationRecordResource\Pages;

use App\Filament\App\Resources\QualificationRecordResource;
use App\Models\QualificationRecord;
use App\Traits\Filament\InteractsWithBatchRecords;
use App\Traits\Filament\InteractsWithModelNotifications;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class CreateQualificationRecord extends CreateRecord
{
    use InteractsWithBatchRecords;
    use InteractsWithModelNotifications;

    protected static string $resource = QualificationRecordResource::class;

    public function mount(): void
    {
        parent::mount();

        $livewire = $this->form->getLivewire();
        $statePath = $this->form->getStatePath();
        data_set($livewire, $statePath.'.user_id', Arr::wrap(request()->query('user_id')));
    }

    protected function handleRecordCreation(array $data): Model
    {
        $notificationData = data_get($data, 'model_notifications') ?? [];

        $models = $this->performModelCreations(data_forget($data, 'model_notifications'), function (QualificationRecord $record) use ($notificationData): void {
            $this->performModelNotificationInserts($record, $notificationData);
        });

        return $models->first();
    }
}
