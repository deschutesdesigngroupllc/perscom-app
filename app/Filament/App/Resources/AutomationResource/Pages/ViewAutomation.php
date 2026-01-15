<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AutomationResource\Pages;

use App\Filament\App\Resources\AutomationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Components\ViewComponent;

class ViewAutomation extends ViewRecord
{
    protected static string $resource = AutomationResource::class;

    /**
     * @return ViewComponent[]
     */
    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
