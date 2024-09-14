<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Features\ApiAccessFeature;
use App\Filament\App\Resources\PassportTokenResource\Pages;
use App\Models\PassportToken;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Passport\Passport;
use Laravel\Pennant\Feature;

class PassportTokenResource extends BaseResource
{
    protected static ?string $model = PassportToken::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'Integrations';

    protected static ?int $navigationSort = 6;

    protected static ?string $pluralModelLabel = 'API keys';

    protected static ?string $modelLabel = 'API key';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('API Key Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->helperText('An identifying name for the API key')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('scopes')
                            ->helperText(fn ($operation) => match ($operation) {
                                'edit' => 'Please create a new API key to change the scopes.',
                                default => 'The scopes that the API key will have access to.'
                            })
                            ->searchable()
                            ->multiple()
                            ->live()
                            ->disabled(fn ($operation) => $operation !== 'create')
                            ->options(fn () => Passport::scopes()->pluck('id', 'id')->sort())
                            ->hidden(fn (Forms\Get $get) => $get('all_scopes')),
                        Forms\Components\Checkbox::make('all_scopes')
                            ->visibleOn('create')
                            ->default(true)
                            ->live()
                            ->inline()
                            ->helperText('Select to allow access to all scopes.'),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('name'),
                TextEntry::make('expires_at')
                    ->dateTime(),
                TextEntry::make('token')
                    ->label('API key')
                    ->badge()
                    ->color('gray')
                    ->helperText('Click to copy the API key to your clipboard.')
                    ->copyable()
                    ->columnSpan(1),
                TextEntry::make('scopes')
                    ->columnSpanFull()
                    ->listWithLineBreaks()
                    ->limitList()
                    ->expandableLimitedList()
                    ->badge(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('scopes')
                    ->badge()
                    ->listWithLineBreaks()
                    ->limitList()
                    ->expandableLimitedList()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups(['revoked'])
            ->filters([
                Tables\Filters\TernaryFilter::make('revoked'),
                Tables\Filters\SelectFilter::make('scopes')
                    ->multiple()
                    ->searchable()
                    ->options(fn () => Passport::scopes()->pluck('id', 'id')->sort())
                    ->query(fn (Builder $query, $data) => $query->when(data_get($data, 'values'), fn (Builder $query) => $query->whereJsonContains('scopes', data_get($data, 'values')))),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPassportTokens::route('/'),
            'create' => Pages\CreatePassportToken::route('/create'),
            'view' => Pages\ViewPassportToken::route('/{record}'),
            'edit' => Pages\EditPassportToken::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return parent::canAccess() && Feature::active(ApiAccessFeature::class);
    }
}
