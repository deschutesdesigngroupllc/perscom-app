<?php

declare(strict_types=1);

namespace App\Traits\Filament;

use App\Contracts\SendsModelNotifications;
use App\Models\ModelNotification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin EditRecord
 */
trait InteractsWithModelNotifications
{
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data = $this->fillModelNotificationData($this->record, $data);

        return parent::mutateFormDataBeforeFill($data);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $this->performModelNotificationInserts($record, $data);

        return parent::handleRecordUpdate($record, data_forget($data, 'model_notifications'));
    }

    protected function handleRecordCreation(array $data): Model
    {
        $notificationData = data_get($data, 'model_notifications');

        $record = parent::handleRecordCreation(data_forget($data, 'model_notifications'));

        $this->performModelNotificationInserts($record, $notificationData);

        return $record;
    }

    protected function performModelNotificationInserts(SendsModelNotifications|Model $record, array $data): void
    {
        $record->modelNotifications()->delete();

        if (data_get($data, 'enabled', false)) {
            $subject = data_get($data, 'subject', '');
            $message = data_get($data, 'message', '');
            $channels = data_get($data, 'channels', []);

            $event = 'created';
            if (method_exists(static::$resource, 'modelNotificationCreatedEvent')) {
                $event = static::$resource::modelNotificationCreatedEvent();
            }

            collect(data_get($data, 'groups'))->each(fn ($groupId) => $record->modelNotifications()->create(ModelNotification::forGroup($groupId, $event, $subject, $message, $channels)));
            collect(data_get($data, 'units'))->each(fn ($unitId) => $record->modelNotifications()->create(ModelNotification::forUnit($unitId, $event, $subject, $message, $channels)));
            collect(data_get($data, 'users'))->each(fn ($userId) => $record->modelNotifications()->create(ModelNotification::forUser($userId, $event, $subject, $message, $channels)));
        }
    }

    protected function fillModelNotificationData(SendsModelNotifications|Model $record, array $data): array
    {
        $notifications = $record->modelNotifications;

        data_set($data, 'model_notifications.groups', $notifications->whereNotNull('group_id')->pluck('group_id')->toArray());
        data_set($data, 'model_notifications.units', $notifications->whereNotNull('unit_id')->pluck('unit_id')->toArray());
        data_set($data, 'model_notifications.users', $notifications->whereNotNull('user_id')->pluck('user_id')->toArray());

        data_set($data, 'model_notifications.enabled', filled($notifications));
        data_set($data, 'model_notifications.subject', data_get($notifications->whereNotNull('subject')->first(), 'subject'));
        data_set($data, 'model_notifications.message', data_get($notifications->whereNotNull('message')->first(), 'message'));
        data_set($data, 'model_notifications.channels', data_get($notifications->whereNotNull('channels')->first(), 'channels'));

        return $data;
    }
}
