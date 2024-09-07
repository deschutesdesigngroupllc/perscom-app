<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Logs\Resources;

use App\Features\WebhookFeature;
use App\Filament\App\Clusters\Logs;
use App\Filament\App\Clusters\Logs\Resources\WebhookLogResource\Pages;
use App\Filament\App\Resources\BaseResource;
use App\Models\WebhookLog;
use Filament\Facades\Filament;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Laravel\Pennant\Feature;
use Parallax\FilamentSyntaxEntry\SyntaxEntry;

class WebhookLogResource extends BaseResource
{
    protected static ?string $model = WebhookLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $cluster = Logs::class;

    protected static ?string $label = 'Webhook Logs';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('causer.label')
                    ->badge()
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->iconPosition(IconPosition::After)
                    ->url(function (?WebhookLog $record) {
                        $resource = Filament::getModelResource($record->causer);

                        return $resource ? $resource::getUrl('edit', [
                            'record' => $record->causer,
                        ]) : false;
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->label('Webhook')
                    ->color('gray')
                    ->badge(),
                Tables\Columns\TextColumn::make('event')
                    ->color('gray')
                    ->badge()
                    ->getStateUsing(fn (WebhookLog $record) => $record->getExtraProperty('event')),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->label('Sent')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
                            TextEntry::make('created_at')
                                ->dateTime()
                                ->label('Sent'),
                            TextEntry::make('event')
                                ->badge()
                                ->color('gray'),
                            TextEntry::make('causer.label')
                                ->badge()
                                ->openUrlInNewTab()
                                ->icon('heroicon-o-arrow-top-right-on-square')
                                ->iconPosition(IconPosition::After)
                                ->url(function (?WebhookLog $record) {
                                    $resource = Filament::getModelResource($record->causer);

                                    return $resource ? $resource::getUrl('edit', [
                                        'record' => $record->causer,
                                    ]) : false;
                                }),
                            SyntaxEntry::make('data')
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

    public static function canAccess(): bool
    {
        return parent::canAccess() && Feature::active(WebhookFeature::class);
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
