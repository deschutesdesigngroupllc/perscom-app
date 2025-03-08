<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\WebhookResource\Pages;

use App\Filament\App\Clusters\Logs\Resources\WebhookLogResource;
use App\Filament\App\Resources\WebhookResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWebhooks extends ListRecords
{
    protected static string $resource = WebhookResource::class;

    protected ?string $subheading = 'Webhooks provide real-time outbound notifications to ensure your services stay in sync.';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('logs')
                ->label('View logs')
                ->color('gray')
                ->url(fn (): string => WebhookLogResource::getUrl()),
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            WebhookResource\Widgets\WebhookRequests::class,
        ];
    }
}
