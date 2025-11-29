<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AttachmentResource\Pages;

use App\Filament\App\Resources\AttachmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAttachments extends ListRecords
{
    protected static string $resource = AttachmentResource::class;

    protected ?string $subheading = 'A record of all attachments in your account.';

    /**
     * @return CreateAction[]
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
