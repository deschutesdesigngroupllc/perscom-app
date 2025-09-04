<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\WebhookResource\Pages;

use App\Filament\App\Resources\WebhookResource;
use App\Models\Enums\WebhookEvent;
use App\Models\Webhook;
use App\Services\WebhookService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CodeEditor;
use Filament\Resources\Pages\ViewRecord;

class ViewWebhook extends ViewRecord
{
    protected static string $resource = WebhookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('test')
                ->label('Test webhook')
                ->schema([
                    CodeEditor::make('payload')
                        ->required()
                        ->default(fn () => json_encode([
                            'status' => 'ok',
                        ], JSON_PRETTY_PRINT))
                        ->json()
                        ->helperText('Provide an example JSON payload to test.'),
                ])
                ->color('gray')
                ->modalHeading('Send a test webhook payload')
                ->modalSubmitActionLabel('Send')
                ->successNotificationTitle('The test webhook has been successfully sent.')
                ->action(function (array $data, Action $action, Webhook $record): void {
                    WebhookService::dispatch($record, WebhookEvent::TEST_WEBHOOK->value, json_decode(data_get($data, 'payload', []) ?? [], true));

                    $action->success();
                }),
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
