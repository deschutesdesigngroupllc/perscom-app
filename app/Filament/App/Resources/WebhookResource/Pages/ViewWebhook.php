<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\WebhookResource\Pages;

use App\Filament\App\Resources\WebhookResource;
use App\Models\Enums\WebhookEvent;
use App\Models\Webhook;
use App\Services\WebhookService;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Wiebenieuwenhuis\FilamentCodeEditor\Components\CodeEditor;

class ViewWebhook extends ViewRecord
{
    protected static string $resource = WebhookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('test')
                ->label('Test webhook')
                ->form([
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
                ->action(function (array $data, Actions\Action $action, Webhook $record) {
                    WebhookService::dispatch($record, WebhookEvent::TEST_WEBHOOK->value, json_decode(data_get($data, 'payload', []) ?? [], true));

                    $action->success();
                }),
            Actions\EditAction::make(),
        ];
    }
}
