<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\NewsfeedResource\Pages;

use App\Filament\App\Resources\NewsfeedResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Model;

class CreateNewsfeed extends CreateRecord
{
    protected Width|string|null $maxContentWidth = Width::SevenExtraLarge;

    protected static string $resource = NewsfeedResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        /** @var Model $activity */
        /** @phpstan-ignore varTag.nativeType */
        $activity = activity('newsfeed')
            ->causedBy(User::findOrFail(data_get($data, 'causer_id')))
            ->withProperties([
                'headline' => $headline = data_get($data, 'headline'),
                'text' => data_get($data, 'text'),
            ])->log($headline);

        unset($data['headline'], $data['text']);

        return $activity;
    }
}
