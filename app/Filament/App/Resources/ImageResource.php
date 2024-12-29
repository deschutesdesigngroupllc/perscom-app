<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ImageResource\Pages;
use App\Models\Award;
use App\Models\Image;
use App\Models\Qualification;
use App\Models\Rank;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ImageResource extends BaseResource
{
    protected static ?string $model = Image::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Image')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->helperText('The name of the image.')
                                    ->maxLength(255),
                                Forms\Components\RichEditor::make('description')
                                    ->helperText('A brief description of the image.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                                Forms\Components\FileUpload::make('path')
                                    ->required()
                                    ->label('Image')
                                    ->image()
                                    ->imageEditor()
                                    ->previewable()
                                    ->openable()
                                    ->downloadable()
                                    ->visibility('public')
                                    ->storeFileNamesIn('filename'),
                            ]),
                        Forms\Components\Tabs\Tab::make('Resource')
                            ->icon('heroicon-o-document')
                            ->schema([
                                Forms\Components\MorphToSelect::make('model')
                                    ->preload()
                                    ->hiddenLabel()
                                    ->types([
                                        Forms\Components\MorphToSelect\Type::make(Award::class)
                                            ->titleAttribute('name'),
                                        Forms\Components\MorphToSelect\Type::make(Qualification::class)
                                            ->titleAttribute('name'),
                                        Forms\Components\MorphToSelect\Type::make(Rank::class)
                                            ->titleAttribute('name'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Image')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('description')
                                    ->html(),
                                TextEntry::make('image_url')
                                    ->label('URL')
                                    ->url(fn ($state) => $state)
                                    ->openUrlInNewTab()
                                    ->copyable(),
                                ImageEntry::make('path')
                                    ->label('Image'),
                            ]),
                        Tabs\Tab::make('Resource')
                            ->icon('heroicon-o-document')
                            ->schema([
                                TextEntry::make('model.label')
                                    ->label('Resource')
                                    ->badge()
                                    ->openUrlInNewTab()
                                    ->icon('heroicon-o-arrow-top-right-on-square')
                                    ->iconPosition(IconPosition::After)
                                    ->url(fn (Image $record) => $record->model_url),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ImageColumn::make('path')
                    ->label(''),
                Tables\Columns\TextColumn::make('description')
                    ->formatStateUsing(fn ($state) => Str::limit($state))
                    ->html()
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('image_url')
                    ->label('URL')
                    ->url(fn ($state) => $state)
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->iconPosition(IconPosition::After)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('model.label')
                    ->label('Resource')
                    ->badge()
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->iconPosition(IconPosition::After)
                    ->url(fn (Image $record) => $record->model_url),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (?Image $record) => $record->image_url)
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListImages::route('/'),
            'create' => Pages\CreateImage::route('/create'),
            'edit' => Pages\EditImage::route('/{record}/edit'),
            'view' => Pages\ViewImage::route('/{record}'),
        ];
    }
}
