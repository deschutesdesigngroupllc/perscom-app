<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AutomationResource\Pages;

use App\Filament\App\Resources\AutomationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Components\ViewComponent;

class EditAutomation extends EditRecord
{
    protected static string $resource = AutomationResource::class;

    /**
     * @return ViewComponent[]
     */
    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
