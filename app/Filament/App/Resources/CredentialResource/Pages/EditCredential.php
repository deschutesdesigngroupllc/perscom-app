<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\CredentialResource\Pages;

use App\Filament\App\Resources\CredentialResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCredential extends EditRecord
{
    protected static string $resource = CredentialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
