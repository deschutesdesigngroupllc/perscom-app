<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\AutomationLogResource\Pages\ListAutomationLogs;
use App\Filament\App\Resources\AutomationLogResource\Pages\ViewAutomationLog;
use App\Filament\App\Resources\AutomationResource\RelationManagers\LogsRelationManager;
use App\Models\AutomationLog;
use App\Models\Enums\AutomationLogStatus;
use App\Models\Enums\AutomationTrigger;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\CodeEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Phiki\Grammar\Grammar;
use UnitEnum;

class AutomationLogResource extends BaseResource
{
    protected static ?string $model = AutomationLog::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationParentItem = 'Automations';

    protected static string|UnitEnum|null $navigationGroup = 'Integrations';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Logs';

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateActions([
                Action::make('new')
                    ->label('New automation')
                    ->url(AutomationResource::getUrl('create')),
            ])
            ->emptyStateDescription('There are no automation logs to view. Create your first automation to start automating tasks.')
            ->columns([
                TextColumn::make('automation.name')
                    ->hiddenOn(LogsRelationManager::class)
                    ->label('Automation')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('trigger')
                    ->hiddenOn(LogsRelationManager::class)
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('subject_type')
                    ->label('Subject')
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-')
                    ->toggleable(),
                TextColumn::make('execution_time_ms')
                    ->label('Time')
                    ->suffix(' ms')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->since()
                    ->toggleable(false)
                    ->sortable()
                    ->label('Executed'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(AutomationLogStatus::class),
                SelectFilter::make('trigger')
                    ->options(AutomationTrigger::groupedOptions())
                    ->searchable(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
                        Tab::make('Overview')
                            ->icon('heroicon-o-bolt')
                            ->schema([
                                TextEntry::make('automation.name')
                                    ->label('Automation')
                                    ->badge()
                                    ->color('gray'),
                                TextEntry::make('trigger')
                                    ->badge()
                                    ->color('gray'),
                                TextEntry::make('status')
                                    ->badge(),
                                TextEntry::make('execution_time_ms')
                                    ->label('Execution Time')
                                    ->suffix(' ms'),
                                TextEntry::make('created_at')
                                    ->label('Executed At')
                                    ->dateTime(),
                                TextEntry::make('error_message')
                                    ->label('Error Message')
                                    ->visible(fn ($state): bool => filled($state))
                                    ->color('danger')
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Subject')
                            ->icon('heroicon-o-cube')
                            ->schema([
                                TextEntry::make('subject_type')
                                    ->label('Type')
                                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-'),
                                TextEntry::make('subject_id')
                                    ->label('ID'),
                                TextEntry::make('causer_type')
                                    ->label('Triggered By Type')
                                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-')
                                    ->visible(fn ($state): bool => filled($state)),
                                TextEntry::make('causer_id')
                                    ->label('Triggered By ID')
                                    ->visible(fn ($state): bool => filled($state)),
                            ]),
                        Tab::make('Condition')
                            ->icon('heroicon-o-funnel')
                            ->schema([
                                TextEntry::make('condition_expression')
                                    ->label('Expression')
                                    ->placeholder('No Condition Configured')
                                    ->copyable()
                                    ->columnSpanFull(),
                                IconEntry::make('condition_result')
                                    ->label('Result')
                                    ->boolean()
                                    ->visible(fn (AutomationLog $record): bool => $record->condition_result !== null),
                            ]),
                        Tab::make('Context')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                CodeEntry::make('context')
                                    ->hiddenLabel()
                                    ->grammar(Grammar::Json)
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Action Result')
                            ->icon('heroicon-o-paper-airplane')
                            ->schema([
                                CodeEntry::make('action_payload')
                                    ->default('No Result Logged')
                                    ->hiddenLabel()
                                    ->grammar(Grammar::Json)
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListAutomationLogs::route('/'),
            'view' => ViewAutomationLog::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }
}
