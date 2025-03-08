<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TenantResource\RelationManagers;

use App\Models\Domain;
use App\Rules\SubdomainRule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DomainsRelationManager extends RelationManager
{
    protected static string $relationship = 'domains';

    protected static ?string $icon = 'heroicon-o-globe-alt';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('domain')
                    ->helperText('The tenant\'s subdomain.')
                    ->columnSpanFull()
                    ->rule(new SubdomainRule)
                    ->unique('domains', 'domain', ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_custom_subdomain')
                    ->helperText('Is the domain a custom domain.')
                    ->label('Custom subdomain')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('domain')
            ->columns([
                Tables\Columns\TextColumn::make('domain')
                    ->sortable(),
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
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
