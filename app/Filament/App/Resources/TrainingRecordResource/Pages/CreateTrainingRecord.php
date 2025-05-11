<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\TrainingRecordResource\Pages;

use App\Filament\App\Resources\TrainingRecordResource;
use App\Models\TrainingRecord;
use App\Traits\Filament\InteractsWithBatchRecords;
use App\Traits\Filament\InteractsWithModelNotifications;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class CreateTrainingRecord extends CreateRecord
{
    use InteractsWithBatchRecords;
    use InteractsWithModelNotifications;

    protected static string $resource = TrainingRecordResource::class;

    public function mount(): void
    {
        parent::mount();

        $livewire = $this->form->getLivewire();
        $statePath = $this->form->getStatePath();
        data_set($livewire, "$statePath.user_id", Arr::wrap(request()->query('user_id')));
    }

    protected function handleRecordCreation(array $data): Model
    {
        $notificationData = data_get($data, 'model_notifications') ?? [];

        $models = $this->performModelCreations(data_forget($data, 'model_notifications'), function (TrainingRecord $record) use ($notificationData): void {
            $this->performModelNotificationInserts($record, $notificationData);
        });

        return $models->first();
    }
}
