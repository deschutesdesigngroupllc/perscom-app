<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DomainResource\Pages\CreateDomain;
use App\Filament\Admin\Resources\DomainResource\Pages\EditDomain;
use App\Filament\Admin\Resources\DomainResource\Pages\ListDomains;
use App\Models\Domain;
use App\Rules\SubdomainRule;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

class DomainResource extends Resource
{
    protected static ?string $model = Domain::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static string|UnitEnum|null $navigationGroup = 'Application';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('domain')
                    ->helperText("The tenant's subdomain.")
                    ->required()
                    ->rule(new SubdomainRule)
                    ->unique('domains', 'domain', ignoreRecord: true)
                    ->prefix(config('app.scheme').'://')
                    ->suffix(config('app.base_url'))
                    ->maxLength(255),
                Select::make('tenant_id')
                    ->helperText('The tenant the domain will resolve to.')
                    ->relationship('tenant', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Toggle::make('is_custom_subdomain')
                    ->helperText('Is the domain a custom domain.')
                    ->label('Custom Subdomain')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateDescription('There are no domains to display.')
            ->columns([
                TextColumn::make('tenant.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('domain')
                    ->sortable()
                    ->searchable(),
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
                TextColumn::make('created_at')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->groups(['tenant.name'])
            ->filters([
                TernaryFilter::make('is_custom_subdomain')
                    ->label('Custom subdomain'),
                SelectFilter::make('tenant_id')
                    ->label('Tenant')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->relationship('tenant', 'name'),
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

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListDomains::route('/'),
            'create' => CreateDomain::route('/create'),
            'edit' => EditDomain::route('/{record}/edit'),
        ];
    }
}
