<?php

declare(strict_types=1);

namespace App\Traits\Filament;

use Closure;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @mixin CreateRecord
 */
trait InteractsWithBatchRecords
{
    protected function handleRecordCreation(array $data): Model
    {
        $records = $this->performModelCreations($data);

        return $records->first();
    }

    protected function performModelCreations(array $data, ?Closure $callback = null): Collection
    {
        return collect(data_get($data, 'user_id'))->map(function ($userId) use ($data, $callback) {
            data_set($data, 'user_id', $userId);

            return tap(parent::handleRecordCreation($data), fn ($model) => value($callback, $model));
        });
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
