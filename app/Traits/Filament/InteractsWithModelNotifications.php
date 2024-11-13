<?php

declare(strict_types=1);

namespace App\Traits\Filament;

use App\Models\ModelNotification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @mixin EditRecord
 */
trait InteractsWithModelNotifications
{
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (method_exists($this->record, 'modelNotifications')) {
            /** @var Collection $notifications */
            $notifications = $this->record->modelNotifications;

            data_set($data, 'model_notifications.groups', $notifications->whereNotNull('group_id')->pluck('group_id')->toArray());
            data_set($data, 'model_notifications.units', $notifications->whereNotNull('unit_id')->pluck('unit_id')->toArray());
            data_set($data, 'model_notifications.users', $notifications->whereNotNull('user_id')->pluck('user_id')->toArray());

            data_set($data, 'model_notifications.subject', data_get($notifications->whereNotNull('message')->first(), 'subject'));
            data_set($data, 'model_notifications.message', data_get($notifications->whereNotNull('message')->first(), 'message'));
        }

        return parent::mutateFormDataBeforeFill($data);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (method_exists($record, 'modelNotifications')) {
            $record->modelNotifications()->delete();

            $subject = data_get($data, 'model_notifications.subject', '');
            $message = data_get($data, 'model_notifications.message', '');

            collect(data_get($data, 'model_notifications.groups'))->each(fn ($groupId) => $record->modelNotifications()->create(ModelNotification::forGroup($groupId, 'created', $message)));
            collect(data_get($data, 'model_notifications.units'))->each(fn ($unitId) => $record->modelNotifications()->create(ModelNotification::forUnit($unitId, 'created', $message)));
            collect(data_get($data, 'model_notifications.users'))->each(fn ($userId) => $record->modelNotifications()->create(ModelNotification::forUser($userId, 'created', $message)));
        }

        return parent::handleRecordUpdate($record, data_forget($data, 'model_notifications'));
    }

    protected function handleRecordCreation(array $data): Model
    {
        $notificationData = data_get($data, 'model_notifications');

        $record = parent::handleRecordCreation(data_forget($data, 'model_notifications'));

        if (method_exists($record, 'modelNotifications')) {
            $subject = data_get($notificationData, 'message', '');
            $message = data_get($notificationData, 'message', '');

            collect(data_get($notificationData, 'groups'))->each(fn ($groupId) => $record->modelNotifications()->create(ModelNotification::forGroup($groupId, 'created', $message)));
            collect(data_get($notificationData, 'units'))->each(fn ($unitId) => $record->modelNotifications()->create(ModelNotification::forUnit($unitId, 'created', $message)));
            collect(data_get($notificationData, 'users'))->each(fn ($userId) => $record->modelNotifications()->create(ModelNotification::forUser($userId, 'created', $message)));
        }

        return $record;
    }
}
