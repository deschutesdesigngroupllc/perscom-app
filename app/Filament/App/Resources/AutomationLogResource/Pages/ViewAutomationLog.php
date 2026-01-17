<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AutomationLogResource\Pages;

use App\Filament\App\Resources\AutomationLogResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAutomationLog extends ViewRecord
{
    protected static string $resource = AutomationLogResource::class;

    /**
     * @return Action[]
     */
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
