<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AttachmentResource\Pages;

use App\Filament\App\Resources\AttachmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttachment extends EditRecord
{
    protected static string $resource = AttachmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
