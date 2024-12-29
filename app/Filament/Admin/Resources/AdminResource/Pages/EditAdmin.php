<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\AdminResource\Pages;

use App\Filament\Admin\Resources\AdminResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdmin extends EditRecord
{
    protected static string $resource = AdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
