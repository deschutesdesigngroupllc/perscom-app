<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\WebhookResource\Pages;

use App\Filament\App\Resources\WebhookLogResource;
use App\Filament\App\Resources\WebhookResource;
use App\Filament\App\Resources\WebhookResource\Widgets\WebhookRequests;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Components\ViewComponent;

class ListWebhooks extends ListRecords
{
    protected static string $resource = WebhookResource::class;

    protected ?string $subheading = 'Webhooks provide real-time outbound notifications to ensure your services stay in sync.';

    /**
     * @return ViewComponent[]
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('logs')
                ->label('View logs')
                ->color('gray')
                ->url(fn (): string => WebhookLogResource::getUrl()),
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            WebhookRequests::class,
        ];
    }
}
