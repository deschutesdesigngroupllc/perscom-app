<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AwardResource\Pages;

use App\Filament\App\Resources\AwardResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAward extends EditRecord
{
    protected static string $resource = AwardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
