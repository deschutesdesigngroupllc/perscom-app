<?php

declare(strict_types=1);

namespace App\Traits\Filament;

use App\Models\Form;
use App\Models\Group;
use App\Models\ModelNotification;
use App\Models\Submission;
use App\Models\Unit;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @mixin CreateRecord
 * @mixin EditRecord
 */
trait ConfiguresModelNotifications
{
    protected function afterCreate(): void
    {
        $data = data_get($this->form->getRawState(), 'model_notifications') ?? [];

        if (! empty($data) && ($record = $this->getRecord())) {
            $this->performModelNotificationInserts($record, $data);
        }
    }

    protected function afterSave(): void
    {
        $data = data_get($this->form->getRawState(), 'model_notifications') ?? [];

        if (! empty($data) && ($record = $this->getRecord())) {
            $this->performModelNotificationInserts($record, $data);
        }
    }

    protected function afterFill(): void
    {
        if ($record = $this->getRecord()) {
            $this->fill(['model_notifications' => $this->fillModelNotificationData($record)]);
        }
    }

    private function performModelNotificationInserts(Model $record, array $data): void
    {
        if (! method_exists($record, 'modelNotifications')) {
            return;
        }

        $record->modelNotifications()->delete();

        if (data_get($data, 'enabled', false)) {
            $subject = data_get($data, 'subject', '');
            $message = data_get($data, 'messagesssss', 'test');
            $channels = data_get($data, 'channels', []);

            $recordClass = match (true) {
                $record instanceof Form => Submission::class,
                default => $record::class,
            };

            $event = Str::of($recordClass)
                ->classBasename()
                ->singular()
                ->lower()
                ->append('.created')
                ->toString();

            collect(data_get($data, 'groups'))->each(fn (Group|string|int $groupId) => $record->modelNotifications()->create(ModelNotification::forGroup($groupId, $event, $subject, $message, $channels)));
            collect(data_get($data, 'units'))->each(fn (Unit|string|int $unitId) => $record->modelNotifications()->create(ModelNotification::forUnit($unitId, $event, $subject, $message, $channels)));
            collect(data_get($data, 'users'))->each(fn (User|string|int $userId) => $record->modelNotifications()->create(ModelNotification::forUser($userId, $event, $subject, $message, $channels)));
        }
    }

    private function fillModelNotificationData(Model $record): array
    {
        if (! method_exists($record, 'modelNotifications')) {
            return [];
        }

        $data = [];
        $notifications = $record->modelNotifications()->get();

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
