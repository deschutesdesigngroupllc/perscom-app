<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\IssuerResource\Pages;

use App\Filament\App\Resources\IssuerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIssuers extends ListRecords
{
    protected static string $resource = IssuerResource::class;

    protected ?string $subheading = 'The organization or entity that authorizes and provides an official document, certificate, or digital badge to personnel, verifying their competencies, credentials, or qualifications.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
