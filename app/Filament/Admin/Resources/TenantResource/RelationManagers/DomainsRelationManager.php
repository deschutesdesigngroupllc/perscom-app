<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TenantResource\RelationManagers;

use App\Models\Domain;
use App\Rules\SubdomainRule;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DomainsRelationManager extends RelationManager
{
    protected static string $relationship = 'domains';

    protected static string|BackedEnum|null $icon = 'heroicon-o-globe-alt';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('domain')
                    ->helperText('The tenant\'s subdomain.')
                    ->columnSpanFull()
                    ->rule(new SubdomainRule)
                    ->unique('domains', 'domain', ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
                Toggle::make('is_custom_subdomain')
                    ->helperText('Is the domain a custom domain.')
                    ->label('Custom subdomain')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->description('The domains associated with the tenant.')
            ->emptyStateDescription('The tenant has no domains.')
            ->recordTitleAttribute('domain')
            ->columns([
                TextColumn::make('domain')
                    ->sortable(),
                TextColumn::make('type')
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
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
