<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\NewsfeedResource\Pages;

use App\Filament\App\Resources\NewsfeedResource;
use App\Models\Newsfeed;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class EditNewsfeed extends EditRecord
{
    protected static string $resource = NewsfeedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    /**
     * @param  Newsfeed  $record
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Collection<string, mixed> $properties */
        $properties = collect($record->properties);

        $record->forceFill([
            'properties' => $properties
                ->put('headline', data_get($data, 'headline'))
                ->put('text', data_get($data, 'text')),
        ])->save();

        data_forget($data, 'headline');
        data_forget($data, 'text');

        return parent::handleRecordUpdate($record, $data);
    }
}
