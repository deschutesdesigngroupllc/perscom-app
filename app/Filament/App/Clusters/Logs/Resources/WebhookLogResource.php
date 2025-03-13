<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Logs\Resources;

use App\Filament\App\Clusters\Logs;
use App\Filament\App\Clusters\Logs\Resources\WebhookLogResource\Pages;
use App\Filament\App\Resources\BaseResource;
use App\Filament\App\Resources\WebhookResource;
use App\Models\Enums\WebhookEvent;
use App\Models\WebhookLog;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Panel;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Parallax\FilamentSyntaxEntry\SyntaxEntry;

class WebhookLogResource extends BaseResource
{
    protected static ?string $model = WebhookLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $cluster = Logs::class;

    protected static ?string $label = 'Webhook Logs';

    public static function isTenantSubscriptionRequired(Panel $panel): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateActions([
                Tables\Actions\Action::make('new')
                    ->label('New webhook')
                    ->url(WebhookResource::getUrl('create')),
            ])
            ->emptyStateDescription('Create your first webhook to start sending real-time notifications.')
            ->columns([
                Tables\Columns\TextColumn::make('request_id')
                    ->copyable()
                    ->label('Request ID')
                    ->sortable()
                    ->searchable(['properties']),
                Tables\Columns\TextColumn::make('causer.label')
                    ->label('Resource')
                    ->badge()
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->iconPosition(IconPosition::After)
                    ->url(fn (WebhookLog $record) => $record->resource_url),
                Tables\Columns\TextColumn::make('description')
                    ->label('Webhook')
                    ->sortable()
                    ->color('gray')
                    ->badge(),
                Tables\Columns\TextColumn::make('event')
                    ->sortable()
                    ->color('gray')
                    ->badge(),
                Tables\Columns\TextColumn::make('status_code')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (WebhookLog $record, $state): string => "$state $record->reason_phrase")
                    ->color(fn ($state): string => match (true) {
                        (int) $state >= 200 && (int) $state < 300 => 'success',
                        default => 'danger'
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->toggleable(false)
                    ->sortable()
                    ->label('Sent'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->searchable()
                    ->multiple()
                    ->options(WebhookEvent::class)
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when(
                            filled(data_get($data, 'values')),
                            function (Builder $query) use ($data): void {
                                $query->where(fn (Builder $query) => collect(data_get($data, 'values'))->each(fn (string $event) => $query->orWhereJsonContains('properties->payload->event', $event)));
                            },
                        )),
                Tables\Filters\QueryBuilder::make()
                    ->constraints([
                        Tables\Filters\QueryBuilder\Constraints\Constraint::make('request_id')
                            ->label('Request ID')
                            ->icon('heroicon-o-cloud-arrow-up')
                            ->operators([
                                Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators\ContainsOperator::make()
                                    ->modifyBaseQueryUsing(function (Builder $query, array $settings, bool $isInverse) {
                                        if ($isInverse) {
                                            return $query
                                                ->when(filled(data_get($settings, 'text')),
                                                    function (Builder $query) use ($settings): void {
                                                        $text = data_get($settings, 'text');
                                                        $query->where('properties->payload->request_id', 'NOT LIKE', "%$text%");
                                                    }
                                                );
                                        }

                                        return $query
                                            ->when(filled(data_get($settings, 'text')),
                                                function (Builder $query) use ($settings): void {
                                                    $text = data_get($settings, 'text');
                                                    $query->where('properties->payload->request_id', 'LIKE', "%$text%");
                                                }
                                            );
                                    }),
                            ]),
                        Tables\Filters\QueryBuilder\Constraints\Constraint::make('trace_id')
                            ->label('Trace ID')
                            ->icon('heroicon-o-cloud-arrow-up')
                            ->operators([
                                Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators\ContainsOperator::make()
                                    ->modifyBaseQueryUsing(function (Builder $query, array $settings, bool $isInverse) {
                                        if ($isInverse) {
                                            return $query
                                                ->when(filled(data_get($settings, 'text')),
                                                    function (Builder $query) use ($settings): void {
                                                        $text = data_get($settings, 'text');
                                                        $query->where('properties->payload->trace_id', 'NOT LIKE', "%$text%");
                                                    }
                                                );
                                        }

                                        return $query
                                            ->when(filled(data_get($settings, 'text')),
                                                function (Builder $query) use ($settings): void {
                                                    $text = data_get($settings, 'text');
                                                    $query->where('properties->payload->trace_id', 'LIKE', "%$text%");
                                                }
                                            );
                                    }),
                            ]),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('created_at')
                            ->label('Sent'),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Tabs::make()
                ->columnSpanFull()
                ->tabs([
                    Tabs\Tab::make('Webhook')
                        ->icon('heroicon-o-cloud-arrow-up')
                        ->schema([
                            TextEntry::make('request_id')
                                ->copyable()
                                ->label('Request ID'),
                            TextEntry::make('trace_id')
                                ->copyable()
                                ->label('Trace ID'),
                            TextEntry::make('status_code')
                                ->label('Status')
                                ->badge()
                                ->formatStateUsing(fn (WebhookLog $record, $state): string => "$state $record->reason_phrase")
                                ->color(fn ($state): string => match (true) {
                                    (int) $state >= 200 && (int) $state < 300 => 'success',
                                    default => 'danger'
                                }),
                            TextEntry::make('created_at')
                                ->label('Sent'),
                            TextEntry::make('causer.label')
                                ->label('Resource')
                                ->badge()
                                ->openUrlInNewTab()
                                ->icon('heroicon-o-arrow-top-right-on-square')
                                ->iconPosition(IconPosition::After)
                                ->url(fn (WebhookLog $record) => $record->resource_url),
                            TextEntry::make('description')
                                ->label('Webhook')
                                ->badge()
                                ->color('gray'),
                            TextEntry::make('event')
                                ->badge()
                                ->color('gray'),
                        ]),
                    Tabs\Tab::make('Payload')
                        ->icon('heroicon-o-code-bracket')
                        ->schema([
                            SyntaxEntry::make('payload')
                                ->getStateUsing(fn (WebhookLog $record): mixed => $record->getExtraProperty('payload'))
                                ->hiddenLabel()
                                ->language('json'),
                        ]),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWebhookLogs::route('/'),
            'view' => Pages\ViewWebhookLog::route('/{record}'),
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
