<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AttachmentResource\Pages;

use App\Filament\App\Resources\AttachmentResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateAttachment extends CreateRecord
{
    protected Width|string|null $maxContentWidth = Width::SevenExtraLarge;

    protected static string $resource = AttachmentResource::class;
}
