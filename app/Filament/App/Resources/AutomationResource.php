<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\AutomationResource\Pages\CreateAutomation;
use App\Filament\App\Resources\AutomationResource\Pages\EditAutomation;
use App\Filament\App\Resources\AutomationResource\Pages\ListAutomations;
use App\Filament\App\Resources\AutomationResource\Pages\ViewAutomation;
use App\Filament\App\Resources\AutomationResource\RelationManagers\LogsRelationManager;
use App\Models\Automation;
use App\Models\Enums\AutomationActionType;
use App\Models\Enums\AutomationTrigger;
use App\Models\Message;
use App\Models\Webhook;
use App\Services\AutomationService;
use App\Services\ExpressionLanguageService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\CodeEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class AutomationResource extends BaseResource
{
    protected static ?string $model = Automation::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bolt';

    protected static string|UnitEnum|null $navigationGroup = 'Integrations';

    protected static ?int $navigationSort = 9;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make('Automation')
                            ->icon(Heroicon::OutlinedBolt)
                            ->schema([
                                TextInput::make('name')
                                    ->helperText('A descriptive name for this automation.')
                                    ->required()
                                    ->maxLength(255),
                                RichEditor::make('description')
                                    ->extraInputAttributes(['style' => 'min-height: 10rem;'])
                                    ->helperText('A brief description of what this automation does.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                                TextInput::make('priority')
                                    ->helperText('Lower numbers run first. Default is 0.')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                                Toggle::make('enabled')
                                    ->helperText('Enable or disable this automation.')
                                    ->default(true)
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('1. Trigger')
                            ->icon(Heroicon::OutlinedCursorArrowRays)
                            ->schema([
                                Select::make('trigger')
                                    ->helperText('The event that will trigger this automation.')
                                    ->options(AutomationTrigger::groupedOptions())
                                    ->live()
                                    ->searchable()
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('2. Condition')
                            ->icon(Heroicon::OutlinedFunnel)
                            ->schema([
                                Textarea::make('condition')
                                    ->live(onBlur: true)
                                    ->helperText('An optional expression to evaluate. If it returns false, the action will not run. E.g., model.status == "active"')
                                    ->label('Condition Expression')
                                    ->placeholder('Select a trigger to view     available fields and test a condition.')
                                    ->rows(4)
                                    ->hintActions([
                                        Action::make('testCondition')
                                            ->label('Test')
                                            ->icon('heroicon-o-play')
                                            ->color('success')
                                            ->visible(fn (Get $get): bool => filled($get('trigger')) && filled($get('condition')))
                                            ->action(function (Get $get, Action $action): void {
                                                $trigger = AutomationTrigger::from($get('trigger'));
                                                $context = $trigger->getExampleContext();
                                                $result = app(AutomationService::class)->testCondition(
                                                    $get('condition'),
                                                    $context
                                                );

                                                if (! $result['valid']) {
                                                    $action->failureNotificationTitle('Invalid Expression');
                                                    $action->failureNotificationBody($result['error'] ?? null);
                                                    $action->failure();

                                                    return;
                                                }

                                                if ($result['result']) {
                                                    $action->successNotificationTitle('Condition passed');
                                                    $action->success();
                                                } else {
                                                    $action->failureNotificationTitle('Condition failed');
                                                    $action->failure();
                                                }
                                            }),
                                        Action::make('viewAvailableFunctions')
                                            ->label('View Functions')
                                            ->icon('heroicon-o-command-line')
                                            ->modalHeading('Available Functions')
                                            ->modalDescription('Use these functions in your condition expressions.')
                                            ->modalSubmitAction(false)
                                            ->modalCancelActionLabel('Close')
                                            ->schema(fn (): array => [
                                                CodeEntry::make('functions')
                                                    ->hiddenLabel()
                                                    ->state(collect(ExpressionLanguageService::getAvailableFunctions())
                                                        ->map(fn (string $description, string $signature): string => "{$signature}\n  → {$description}")
                                                        ->implode("\n\n")),
                                            ]),
                                        Action::make('viewAvailableFieldsCondition')
                                            ->label('View Fields')
                                            ->icon('heroicon-o-code-bracket')
                                            ->modalHeading('Available Condition Fields')
                                            ->modalDescription('Use these field paths in your condition. E.g., model.status == "active"')
                                            ->modalSubmitAction(false)
                                            ->modalCancelActionLabel('Close')
                                            ->visible(fn (Get $get): bool => filled($get('trigger')))
                                            ->schema(fn (Get $get): array => [
                                                CodeEntry::make('example')
                                                    ->hiddenLabel()
                                                    ->state(json_encode(
                                                        AutomationTrigger::from($get('trigger'))->getExampleContext(),
                                                        JSON_PRETTY_PRINT
                                                    )),
                                            ]),
                                    ])
                                    ->nullable()
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('3. Action')
                            ->icon(Heroicon::OutlinedPlay)
                            ->schema([
                                Select::make('action_type')
                                    ->label('Action')
                                    ->helperText('The type of action to perform when triggered.')
                                    ->options(AutomationActionType::class)
                                    ->required()
                                    ->live()
                                    ->default(AutomationActionType::WEBHOOK),
                                Fieldset::make('Webhook Configuration')
                                    ->visible(fn (Get $get): bool => $get('action_type') === AutomationActionType::WEBHOOK)
                                    ->schema([
                                        Select::make('webhook_id')
                                            ->columnSpanFull()
                                            ->helperText('Select the webhook to trigger.')
                                            ->label('Webhook')
                                            ->options(Webhook::query()->pluck('url', 'id'))
                                            ->searchable()
                                            ->required(fn (Get $get): bool => $get('action_type') === AutomationActionType::WEBHOOK),
                                        CodeEditor::make('webhook_payload_template')
                                            ->helperText('Optional: Define a custom JSON payload. Use {{ model.field }} for dynamic values. Leave empty to send full model data.')
                                            ->label('Payload Template (JSON)')
                                            ->json()
                                            ->hintAction(
                                                Action::make('viewAvailableFields')
                                                    ->label('View Fields')
                                                    ->icon('heroicon-o-code-bracket')
                                                    ->modalHeading('Available Template Fields')
                                                    ->modalDescription('Use these field paths in your template with {{ field.path }} syntax.')
                                                    ->modalSubmitAction(false)
                                                    ->modalCancelActionLabel('Close')
                                                    ->visible(fn (Get $get): bool => filled($get('trigger')))
                                                    ->schema(fn (Get $get): array => [
                                                        CodeEntry::make('example')
                                                            ->hiddenLabel()
                                                            ->state(json_encode(
                                                                AutomationTrigger::from($get('trigger'))->getExampleContext(),
                                                                JSON_PRETTY_PRINT
                                                            )),
                                                    ])
                                            )
                                            ->columnSpanFull(),
                                    ]),
                                Fieldset::make('Message Configuration')
                                    ->visible(fn (Get $get): bool => $get('action_type') === AutomationActionType::MESSAGE)
                                    ->schema([
                                        Select::make('message_id')
                                            ->columnSpanFull()
                                            ->helperText('Select the message template to use. Channels will be inherited from this message.')
                                            ->label('Message Template')
                                            ->relationship('message', 'message')
                                            ->getOptionLabelFromRecordUsing(fn (Message $record): string => Str::limit(strip_tags($record->message), 50))
                                            ->searchable()
                                            ->preload()
                                            ->required(fn (Get $get): bool => $get('action_type') === AutomationActionType::MESSAGE),
                                        RichEditor::make('message_template')
                                            ->helperText("Override the message content. Use {{ model.field }} for dynamic values. Leave empty to use the selected message's content.")
                                            ->label('Message Content Override')
                                            ->extraInputAttributes(['style' => 'min-height: 10rem;'])
                                            ->hintAction(
                                                Action::make('viewAvailableFieldsMessage')
                                                    ->label('View Fields')
                                                    ->icon('heroicon-o-code-bracket')
                                                    ->modalHeading('Available Template Fields')
                                                    ->modalDescription('Use these field paths in your template with {{ field.path }} syntax.')
                                                    ->modalSubmitAction(false)
                                                    ->modalCancelActionLabel('Close')
                                                    ->visible(fn (Get $get): bool => filled($get('trigger')))
                                                    ->schema(fn (Get $get): array => [
                                                        CodeEntry::make('example')
                                                            ->hiddenLabel()
                                                            ->state(json_encode(
                                                                AutomationTrigger::from($get('trigger'))->getExampleContext(),
                                                                JSON_PRETTY_PRINT
                                                            )),
                                                    ])
                                            )
                                            ->nullable()
                                            ->columnSpanFull(),
                                        Textarea::make('message_recipients_expression')
                                            ->helperText('Expression to determine recipients. E.g., [model.user_id] or [model.user.id]. Leave empty to use context user.')
                                            ->label('Recipients Expression')
                                            ->rows(2)
                                            ->nullable()
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Automation')
                            ->icon('heroicon-o-bolt')
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('description')
                                    ->visible(fn ($state): bool => filled($state))
                                    ->html(),
                                TextEntry::make('priority'),
                                IconEntry::make('enabled')
                                    ->boolean(),
                            ]),
                        Tab::make('1. Trigger')
                            ->icon(Heroicon::OutlinedCursorArrowRays)
                            ->schema([
                                TextEntry::make('trigger')
                                    ->badge()
                                    ->color('gray'),
                            ]),
                        Tab::make('2. Condition')
                            ->icon('heroicon-o-funnel')
                            ->schema([
                                TextEntry::make('condition')
                                    ->label('Condition Expression')
                                    ->placeholder('No Condition Configured')
                                    ->copyable(),
                            ]),
                        Tab::make('3. Action')
                            ->icon('heroicon-o-play')
                            ->schema([
                                TextEntry::make('action_type')
                                    ->label('Action')
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('webhook.url')
                                    ->label('Webhook URL')
                                    ->visible(fn (Automation $record): bool => $record->isWebhookAction())
                                    ->copyable(),
                                TextEntry::make('message.message')
                                    ->label('Source Message')
                                    ->visible(fn (Automation $record): bool => $record->isMessageAction())
                                    ->html()
                                    ->limit(100),
                                TextEntry::make('message_template')
                                    ->label('Message Content Override')
                                    ->visible(fn (Automation $record): bool => $record->isMessageAction() && filled($record->message_template))
                                    ->html(),
                                TextEntry::make('message_recipients_expression')
                                    ->label('Recipients Expression')
                                    ->visible(fn (Automation $record): bool => $record->isMessageAction() && filled($record->message_recipients_expression))
                                    ->copyable(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateIcon(Heroicon::OutlinedBolt)
            ->emptyStateDescription('Create your first automation to start automating tasks.')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->placeholder('No Description')
                    ->formatStateUsing(fn ($state) => Str::limit($state))
                    ->html()
                    ->wrap()
                    ->toggleable(),
                TextColumn::make('trigger')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                TextColumn::make('action_type')
                    ->label('Action')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                IconColumn::make('enabled')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('priority')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('logs_count')
                    ->label('Executions')
                    ->counts('logs')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->toggleable(false)
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('trigger')
                    ->options(AutomationTrigger::groupedOptions())
                    ->searchable(),
                SelectFilter::make('action_type')
                    ->options(AutomationActionType::class),
                TernaryFilter::make('enabled'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListAutomations::route('/'),
            'create' => CreateAutomation::route('/create'),
            'edit' => EditAutomation::route('/{record}/edit'),
            'view' => ViewAutomation::route('/{record}'),
        ];
    }

    /**
     * @return class-string[]
     */
    public static function getRelations(): array
    {
        return [
            LogsRelationManager::class,
        ];
    }
}
