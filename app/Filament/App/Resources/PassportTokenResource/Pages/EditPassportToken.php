<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\PassportTokenResource\Pages;

use App\Filament\App\Resources\PassportTokenResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditPassportToken extends EditRecord
{
    protected static string $resource = PassportTokenResource::class;

    /**
     * @return Action[]
     */
    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (data_get($data, 'scopes') === ['*']) {
            data_set($data, 'all_scopes', true);
        }

        return parent::mutateFormDataBeforeFill($data);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (data_get($data, 'all_scopes')) {
            data_set($data, 'scopes', ['*']);
        }

        data_forget($data, 'all_scopes');

        return parent::handleRecordUpdate($record, $data);
    }
}
