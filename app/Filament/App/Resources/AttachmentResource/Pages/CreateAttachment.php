<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AttachmentResource\Pages;

use App\Filament\App\Resources\AttachmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAttachment extends CreateRecord
{
    protected static string $resource = AttachmentResource::class;
}
