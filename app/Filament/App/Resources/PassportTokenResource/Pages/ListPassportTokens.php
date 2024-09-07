<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\PassportTokenResource\Pages;

use App\Filament\App\Clusters\Logs\Resources\ApiLogResource;
use App\Filament\App\Resources\PassportTokenResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPassportTokens extends ListRecords
{
    protected static string $resource = PassportTokenResource::class;

    protected ?string $subheading = 'Manage your application\'s API keys.';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('logs')
                ->label('View logs')
                ->color('gray')
                ->url(fn () => ApiLogResource::getUrl()),
            Actions\CreateAction::make(),
        ];
    }
}
