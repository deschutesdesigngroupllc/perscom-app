<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\IssuerResource\Pages;

use App\Filament\App\Resources\IssuerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIssuer extends EditRecord
{
    protected static string $resource = IssuerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
