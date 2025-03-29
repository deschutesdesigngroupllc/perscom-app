<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\CredentialResource\Pages;

use App\Filament\App\Resources\CredentialResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCredential extends CreateRecord
{
    protected static string $resource = CredentialResource::class;
}
