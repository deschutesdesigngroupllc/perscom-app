<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\PassportClientResource\Pages;

use App\Filament\App\Resources\PassportClientResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPassportClient extends EditRecord
{
    protected static string $resource = PassportClientResource::class;

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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $scopes = data_get($data, 'all_scopes')
            ? ['*']
            : data_get($data, 'scopes', []);

        data_set($data, 'scopes', $scopes);

        return parent::mutateFormDataBeforeSave(data_forget($data, 'all_scopes'));
    }
}
