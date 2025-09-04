<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\PassportClientResource\Pages;

use App\Filament\App\Resources\ApiLogResource;
use App\Filament\App\Resources\PassportClientResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPassportClients extends ListRecords
{
    protected static string $resource = PassportClientResource::class;

    protected ?string $subheading = 'Manage your SSO clients to enable seamless application-to-application integration with PERSCOM using OAuth 2.0.';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('logs')
                ->label('View logs')
                ->color('gray')
                ->url(fn (): string => ApiLogResource::getUrl()),
            CreateAction::make(),
        ];
    }
}
