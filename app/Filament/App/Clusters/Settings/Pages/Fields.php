<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Settings\Pages;

use App\Filament\App\Clusters\Settings;
use App\Models\Field;
use App\Settings\FieldSettings;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use UnitEnum;

class Fields extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-pencil';

    protected static string|UnitEnum|null $navigationGroup = 'Dashboard';

    protected static string $settings = FieldSettings::class;

    protected static ?string $cluster = Settings::class;

    protected ?string $subheading = 'Manage your default field settings.';

    protected static ?string $slug = 'dashboard/fields';

    protected static ?int $navigationSort = 1;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Assignment Records')
                            ->icon('heroicon-o-rectangle-stack')
                            ->schema([
                                Select::make('assignment_records')
                                    ->label('Custom Fields')
                                    ->options(Field::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload()
                                    ->helperText('Add the specified custom fields to the assignment record form.'),
                            ]),
                        Tab::make('Award Records')
                            ->icon('heroicon-o-trophy')
                            ->schema([
                                Select::make('award_records')
                                    ->label('Custom Fields')
                                    ->options(Field::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload()
                                    ->helperText('Add the specified custom fields to the assignment record form.'),
                            ]),
                        Tab::make('Combat Records')
                            ->icon('heroicon-o-fire')
                            ->schema([
                                Select::make('combat_records')
                                    ->label('Custom Fields')
                                    ->options(Field::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload()
                                    ->helperText('Add the specified custom fields to the assignment record form.'),
                            ]),
                        Tab::make('Qualification Records')
                            ->icon('heroicon-o-star')
                            ->schema([
                                Select::make('qualification_records')
                                    ->label('Custom Fields')
                                    ->options(Field::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload()
                                    ->helperText('Add the specified custom fields to the assignment record form.'),
                            ]),
                        Tab::make('Rank Records')
                            ->icon('heroicon-o-chevron-double-up')
                            ->schema([
                                Select::make('rank_records')
                                    ->label('Custom Fields')
                                    ->options(Field::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload()
                                    ->helperText('Add the specified custom fields to the assignment record form.'),
                            ]),
                        Tab::make('Service Records')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                Select::make('service_records')
                                    ->label('Custom Fields')
                                    ->options(Field::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload()
                                    ->helperText('Add the specified custom fields to the assignment record form.'),
                            ]),
                        Tab::make('Training Records')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                Select::make('training_records')
                                    ->label('Custom Fields')
                                    ->options(Field::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload()
                                    ->helperText('Add the specified custom fields to the assignment record form.'),
                            ]),
                    ]),
            ]);
    }
}
