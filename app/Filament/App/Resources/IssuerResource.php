<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\IssuerResource\Pages\CreateIssuer;
use App\Filament\App\Resources\IssuerResource\Pages\EditIssuer;
use App\Filament\App\Resources\IssuerResource\Pages\ListIssuers;
use App\Filament\Exports\IssuerExporter;
use App\Models\Issuer;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class IssuerResource extends BaseResource
{
    protected static ?string $model = Issuer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static string|UnitEnum|null $navigationGroup = 'Structure';

    protected static ?int $navigationSort = 12;

    protected static ?string $navigationParentItem = 'Qualifications';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make('Issuer')
                            ->icon(Heroicon::OutlinedBriefcase)
                            ->schema([
                                TextInput::make('name')
                                    ->helperText('The name of the issuer.')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateIcon(Heroicon::OutlinedBriefcase)
            ->emptyStateDescription('Issuers are the organizations that grant credentials. Add one before recording credentials.')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                ExportBulkAction::make()
                    ->exporter(IssuerExporter::class)
                    ->icon(Heroicon::OutlinedDocumentArrowDown),
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
            'index' => ListIssuers::route('/'),
            'create' => CreateIssuer::route('/create'),
            'edit' => EditIssuer::route('/{record}/edit'),
        ];
    }
}
