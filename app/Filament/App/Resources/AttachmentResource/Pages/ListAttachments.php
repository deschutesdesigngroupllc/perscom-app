<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AttachmentResource\Pages;

use App\Filament\App\Resources\AttachmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttachments extends ListRecords
{
    protected static string $resource = AttachmentResource::class;

    protected ?string $subheading = 'A record of all attachments in your account.';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
