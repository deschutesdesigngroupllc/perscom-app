<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\WebhookLogResource\Pages;

use App\Filament\App\Resources\WebhookLogResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewWebhookLog extends ViewRecord
{
    protected static string $resource = WebhookLogResource::class;

    /**
     * @return Action[]
     */
    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
