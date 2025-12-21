<?php

declare(strict_types=1);

namespace App\Traits\Filament;

use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;

/**
 * @mixin CreateRecord
 * @mixin EditRecord
 */
trait SavesAndHydratesCustomFieldData
{
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! method_exists(parent::class, 'mutateFormDataBeforeCreate')) {
            return [];
        }

        $data = array_merge($data, data_get($data, 'data') ?? []);

        unset($data['data']);

        return parent::mutateFormDataBeforeCreate($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! method_exists(parent::class, 'mutateFormDataBeforeSave')) {
            return [];
        }

        $data = array_merge($data, data_get($data, 'data') ?? []);

        unset($data['data']);

        return parent::mutateFormDataBeforeSave($data);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (! method_exists(parent::class, 'mutateFormDataBeforeFill')) {
            return [];
        }

        return parent::mutateFormDataBeforeFill(array_merge($data, [
            'data' => $this->getRecord()->getOriginal('data'),
        ]));
    }
}
