<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AutomationResource\Pages;

use App\Filament\App\Resources\AutomationLogResource;
use App\Filament\App\Resources\AutomationResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Components\ViewComponent;

class ListAutomations extends ListRecords
{
    protected static string $resource = AutomationResource::class;

    protected ?string $subheading = 'Automations execute actions automatically when events occur in your system.';

    /**
     * @return ViewComponent[]
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('logs')
                ->label('View logs')
                ->color('gray')
                ->url(fn (): string => AutomationLogResource::getUrl()),
            CreateAction::make(),
        ];
    }
}
