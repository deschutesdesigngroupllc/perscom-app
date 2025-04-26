<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DomainResource\Pages;
use App\Models\Domain;
use App\Rules\SubdomainRule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DomainResource extends Resource
{
    protected static ?string $model = Domain::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationGroup = 'Application';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Forms\Components\TextInput::make('domain')
                    ->helperText('The tenant\'s subdomain.')
                    ->required()
                    ->rule(new SubdomainRule)
                    ->unique('domains', 'domain', ignoreRecord: true)
                    ->prefix(config('app.scheme').'://')
                    ->suffix(config('app.base_url'))
                    ->maxLength(255),
                Forms\Components\Select::make('tenant_id')
                    ->helperText('The tenant the domain will resolve to.')
                    ->relationship('tenant', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Toggle::make('is_custom_subdomain')
                    ->helperText('Is the domain a custom domain.')
                    ->label('Custom Subdomain')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tenant.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('domain')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'Custom Domain' => 'success',
                        default => 'info'
                    })
                    ->getStateUsing(fn (?Domain $record): string => match ($record->is_custom_subdomain) {
                        true => 'Custom Domain',
                        false => 'Fallback Domain'
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->groups(['tenant.name'])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_custom_subdomain')
                    ->label('Custom subdomain'),
                Tables\Filters\SelectFilter::make('tenant_id')
                    ->label('Tenant')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->relationship('tenant', 'name'),
            ])
            ->actions([
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
            'index' => Pages\ListDomains::route('/'),
            'create' => Pages\CreateDomain::route('/create'),
            'edit' => Pages\EditDomain::route('/{record}/edit'),
        ];
    }
}
