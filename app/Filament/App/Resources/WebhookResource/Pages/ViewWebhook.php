<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\WebhookResource\Pages;

use App\Filament\App\Resources\WebhookResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewWebhook extends ViewRecord
{
    protected static string $resource = WebhookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
