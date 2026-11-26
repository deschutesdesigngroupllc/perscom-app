<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\CredentialResource\Pages;

use App\Filament\App\Resources\CredentialResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateCredential extends CreateRecord
{
    protected Width|string|null $maxContentWidth = Width::SevenExtraLarge;

    protected static string $resource = CredentialResource::class;
}
