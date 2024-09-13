<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\NewsfeedResource\Pages;

use App\Filament\App\Resources\NewsfeedResource;
use App\Models\Newsfeed;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditNewsfeed extends EditRecord
{
    protected static string $resource = NewsfeedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * @param  Newsfeed  $record
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->forceFill([
            'properties' => collect($record->properties)
                ->put('headline', data_get($data, 'headline'))
                ->put('text', data_get($data, 'text')),
        ])->save();

        unset($data['headline'], $data['text']);

        return parent::handleRecordUpdate($record, $data);
    }
}
