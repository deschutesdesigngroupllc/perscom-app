<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Logs\Resources;

use App\Features\ApiAccessFeature;
use App\Filament\App\Clusters\Logs;
use App\Filament\App\Clusters\Logs\Resources\ApiLogResource\Pages;
use App\Filament\App\Resources\BaseResource;
use App\Models\ApiLog;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Pennant\Feature;
use Parallax\FilamentSyntaxEntry\SyntaxEntry;
use Symfony\Component\HttpFoundation\Response;

class ApiLogResource extends BaseResource
{
    protected static ?string $model = ApiLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $cluster = Logs::class;

    protected static ?string $label = 'API Logs';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('log_name')
                    ->label('Log')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'api' => 'API',
                        'oauth' => 'OAuth',
                        default => Str::title($state)
                    })
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('Author')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ipAddress')
                    ->label('IP Address'),
                Tables\Columns\TextColumn::make('method')
                    ->badge(),
                Tables\Columns\TextColumn::make('endpoint')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('status')
                    ->color(fn ($state) => match (true) {
                        $state >= 200 && $state < 300 => 'success',
                        default => 'danger',
                    })
                    ->badge()
                    ->suffix(fn ($state) => ' '.Response::$statusTexts[(int) $state]),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->label('Requested'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Tabs::make()
                ->columnSpanFull()
                ->tabs([
                    Tabs\Tab::make('Client')
                        ->icon('heroicon-o-user')
                        ->schema([
                            TextEntry::make('causer.name')
                                ->label('Author'),
                            TextEntry::make('created_at')
                                ->label('Requested'),
                            TextEntry::make('ip_address')
                                ->label('IP Address'),
                        ]),
                    Tabs\Tab::make('Request')
                        ->icon('heroicon-o-cloud-arrow-up')
                        ->schema([
                            TextEntry::make('method')
                                ->badge(),
                            TextEntry::make('endpoint')
                                ->badge()
                                ->color('gray'),
                            KeyValueEntry::make('request_headers')
                                ->label('Headers')
                                ->keyLabel('Header')
                                ->valueLabel('Value')
                                ->getStateUsing(fn (?ApiLog $record) => collect($record->request_headers)->mapWithKeys(fn ($value, $header) => [$header => collect($value)->map(fn ($value) => Str::limit($value))->join(', ')])->toArray()),
                            SyntaxEntry::make('body')
                                ->language('json'),
                        ]),
                    Tabs\Tab::make('Response')
                        ->icon('heroicon-o-cloud-arrow-down')
                        ->schema([
                            TextEntry::make('status')
                                ->suffix(fn ($state) => ' '.Response::$statusTexts[(int) $state])
                                ->color(fn ($state) => match (true) {
                                    $state >= 200 && $state < 300 => 'success',
                                    default => 'danger',
                                })
                                ->badge(),
                            KeyValueEntry::make('request_headers')
                                ->label('Headers')
                                ->keyLabel('Header')
                                ->valueLabel('Value')
                                ->getStateUsing(fn (?ApiLog $record) => collect($record->response_headers)->mapWithKeys(fn ($value, $header) => [$header => collect($value)->join(', ')])->toArray()),
                            SyntaxEntry::make('content')
                                ->language('json'),
                        ]),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApiLogs::route('/'),
            'view' => Pages\ViewApiLog::route('/{record}'),
        ];
    }

    public static function canAccess(): bool
    {
        return parent::canAccess() && Feature::active(ApiAccessFeature::class);
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
