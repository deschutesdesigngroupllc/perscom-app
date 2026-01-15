<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\WebhookLogResource\Pages\ListWebhookLogs;
use App\Filament\App\Resources\WebhookLogResource\Pages\ViewWebhookLog;
use App\Models\Enums\WebhookEvent;
use App\Models\WebhookLog;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\CodeEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators\ContainsOperator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Phiki\Grammar\Grammar;
use UnitEnum;

class WebhookLogResource extends BaseResource
{
    protected static ?string $model = WebhookLog::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationParentItem = 'Webhooks';

    protected static string|UnitEnum|null $navigationGroup = 'Integrations';

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationLabel = 'Logs';

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateActions([
                Action::make('new')
                    ->label('New webhook')
                    ->url(WebhookResource::getUrl('create')),
            ])
            ->emptyStateDescription('There are no webhook logs to view. Create your first webhook to start sending real-time notifications.')
            ->columns([
                TextColumn::make('request_id')
                    ->copyable()
                    ->label('Request ID')
                    ->sortable()
                    ->searchable(['properties']),
                TextColumn::make('causer.label')
                    ->placeholder('No Resource')
                    ->label('Resource')
                    ->badge()
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->iconPosition(IconPosition::After)
                    ->url(fn (WebhookLog $record) => $record->resource_url),
                TextColumn::make('description')
                    ->placeholder('No Description')
                    ->label('Webhook')
                    ->sortable()
                    ->color('gray')
                    ->badge(),
                TextColumn::make('event')
                    ->sortable()
                    ->color('gray')
                    ->badge(),
                TextColumn::make('status_code')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (WebhookLog $record, string $state): string => sprintf('%s %s', $state, $record->reason_phrase))
                    ->color(fn ($state): string => match (true) {
                        (int) $state >= 200 && (int) $state < 300 => 'success',
                        default => 'danger'
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->since()
                    ->toggleable(false)
                    ->sortable()
                    ->label('Sent'),
            ])
            ->filters([
                SelectFilter::make('event')
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
                QueryBuilder::make()
                    ->constraints([
                        Constraint::make('request_id')
                            ->label('Request ID')
                            ->icon('heroicon-o-cloud-arrow-up')
                            ->operators([
                                ContainsOperator::make()
                                    ->modifyBaseQueryUsing(function (Builder $query, array $settings, bool $isInverse) {
                                        if ($isInverse) {
                                            return $query
                                                ->when(filled(data_get($settings, 'text')),
                                                    function (Builder $query) use ($settings): void {
                                                        $text = data_get($settings, 'text');
                                                        $query->where('properties->payload->request_id', 'NOT LIKE', sprintf('%%%s%%', $text));
                                                    }
                                                );
                                        }

                                        return $query
                                            ->when(filled(data_get($settings, 'text')),
                                                function (Builder $query) use ($settings): void {
                                                    $text = data_get($settings, 'text');
                                                    $query->where('properties->payload->request_id', 'LIKE', sprintf('%%%s%%', $text));
                                                }
                                            );
                                    }),
                            ]),
                        Constraint::make('trace_id')
                            ->label('Trace ID')
                            ->icon('heroicon-o-cloud-arrow-up')
                            ->operators([
                                ContainsOperator::make()
                                    ->modifyBaseQueryUsing(function (Builder $query, array $settings, bool $isInverse) {
                                        if ($isInverse) {
                                            return $query
                                                ->when(filled(data_get($settings, 'text')),
                                                    function (Builder $query) use ($settings): void {
                                                        $text = data_get($settings, 'text');
                                                        $query->where('properties->payload->trace_id', 'NOT LIKE', sprintf('%%%s%%', $text));
                                                    }
                                                );
                                        }

                                        return $query
                                            ->when(filled(data_get($settings, 'text')),
                                                function (Builder $query) use ($settings): void {
                                                    $text = data_get($settings, 'text');
                                                    $query->where('properties->payload->trace_id', 'LIKE', sprintf('%%%s%%', $text));
                                                }
                                            );
                                    }),
                            ]),
                        DateConstraint::make('created_at')
                            ->label('Sent'),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
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
                        Tab::make('Webhook')
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
                                    ->formatStateUsing(fn (WebhookLog $record, string $state): string => sprintf('%s %s', $state, $record->reason_phrase))
                                    ->color(fn ($state): string => match (true) {
                                        (int) $state >= 200 && (int) $state < 300 => 'success',
                                        default => 'danger'
                                    }),
                                TextEntry::make('created_at')
                                    ->label('Sent'),
                                TextEntry::make('causer.label')
                                    ->placeholder('No Resource')
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
                        Tab::make('Payload')
                            ->icon('heroicon-o-code-bracket')
                            ->schema([
                                CodeEntry::make('payload')
                                    ->getStateUsing(fn (WebhookLog $record): mixed => $record->getExtraProperty('payload'))
                                    ->hiddenLabel()
                                    ->grammar(Grammar::Json),
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
            'index' => ListWebhookLogs::route('/'),
            'view' => ViewWebhookLog::route('/{record}'),
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
