<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\CompetencyResource\Pages;
use App\Models\Category;
use App\Models\Competency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CompetencyResource extends BaseResource
{
    protected static ?string $model = Competency::class;

    protected static ?string $navigationIcon = 'heroicon-o-numbered-list';

    protected static ?string $navigationGroup = 'Training';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Competency')
                            ->columns()
                            ->icon('heroicon-o-numbered-list')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->helperText('The name of the competency.')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('categories')
                                    ->helperText('An optional category for the competency to assist with organization.')
                                    ->columnSpanFull()
                                    ->relationship('categories', 'name')
                                    ->preload()
                                    ->searchable()
                                    ->multiple()
                                    ->createOptionForm(fn (Form $form): Form => CategoryResource::form($form))
                                    ->createOptionUsing(fn (array $data) => Category::create(array_merge($data, [
                                        'resource' => Competency::class,
                                    ]))->getKey()),
                                Forms\Components\RichEditor::make('description')
                                    ->helperText('A brief description of the competency.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('categories.name')
                    ->listWithLineBreaks(),
            ])
            ->filters([
                SelectFilter::make('categories.name')
                    ->relationship('categories', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->groups(['categories.name'])
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompetencies::route('/'),
            'create' => Pages\CreateCompetency::route('/create'),
            'edit' => Pages\EditCompetency::route('/{record}/edit'),
        ];
    }
}
