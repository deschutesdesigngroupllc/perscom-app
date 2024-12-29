<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\AlertResource\Pages;

use App\Filament\Admin\Resources\AlertResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAlert extends EditRecord
{
    protected static string $resource = AlertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
