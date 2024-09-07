<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\RankResource\Pages;

use App\Filament\App\Resources\RankResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRank extends EditRecord
{
    protected static string $resource = RankResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
