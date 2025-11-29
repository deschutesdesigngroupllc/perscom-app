<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\CredentialResource\Pages;

use App\Filament\App\Resources\CredentialResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCredentials extends ListRecords
{
    protected static string $resource = CredentialResource::class;

    protected ?string $subheading = "An official document, certificate, or digital badge issued by an authorized entity that attests to an individual's qualifications, competencies, or achievements in a specific field or area of expertise";

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
