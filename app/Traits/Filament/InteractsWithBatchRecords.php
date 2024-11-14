<?php

declare(strict_types=1);

namespace App\Traits\Filament;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin CreateRecord
 */
trait InteractsWithBatchRecords
{
    protected function handleRecordCreation(array $data): Model
    {
        $records = collect(data_get($data, 'user_id'))->map(function ($userId) use ($data) {
            data_set($data, 'user_id', $userId);

            return parent::handleRecordCreation($data);
        });

        return $records->first();
    }

    protected function getRedirectUrl(): string
    {
        $users = collect($this->getOldFormState('data.user_id'));

        if ($users->count() > 1) {
            $resource = static::getResource();

            return $resource::getUrl('index');
        }

        return parent::getRedirectUrl();
    }
}
