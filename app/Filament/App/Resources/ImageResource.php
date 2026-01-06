<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ImageResource\Pages\CreateImage;
use App\Filament\App\Resources\ImageResource\Pages\EditImage;
use App\Filament\App\Resources\ImageResource\Pages\ListImages;
use App\Filament\App\Resources\ImageResource\Pages\ViewImage;
use App\Models\Award;
use App\Models\Image;
use App\Models\Qualification;
use App\Models\Rank;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class ImageResource extends BaseResource
{
    protected static ?string $model = Image::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static string|UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Image')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->helperText('The name of the image.')
                                    ->maxLength(255),
                                RichEditor::make('description')
                                    ->extraInputAttributes(['style' => 'min-height: 10rem;'])
                                    ->helperText('A brief description of the image.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                                FileUpload::make('path')
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
                        Tab::make('Resource')
                            ->icon('heroicon-o-document')
                            ->schema([
                                MorphToSelect::make('model')
                                    ->contained(false)
                                    ->preload()
                                    ->label('Resource')
                                    ->types([
                                        Type::make(Award::class)
                                            ->titleAttribute('name'),
                                        Type::make(Qualification::class)
                                            ->titleAttribute('name'),
                                        Type::make(Rank::class)
                                            ->titleAttribute('name'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Image')
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
                        Tab::make('Resource')
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
            ->emptyStateIcon(Heroicon::OutlinedPhoto)
            ->emptyStateDescription('There are no images to view. Create one to get started.')
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                ImageColumn::make('path')
                    ->placeholder('No Image')
                    ->label(''),
                TextColumn::make('description')
                    ->placeholder('No Description')
                    ->formatStateUsing(fn ($state) => Str::limit($state))
                    ->html()
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('image_url')
                    ->limit()
                    ->wrap()
                    ->label('URL')
                    ->url(fn ($state) => $state)
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->iconPosition(IconPosition::After)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('model.label')
                    ->label('Resource')
                    ->badge()
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->iconPosition(IconPosition::After)
                    ->url(fn (Image $record) => $record->model_url),
                TextColumn::make('created_at')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('open')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (?Image $record) => $record->image_url)
                    ->openUrlInNewTab(),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListImages::route('/'),
            'create' => CreateImage::route('/create'),
            'edit' => EditImage::route('/{record}/edit'),
            'view' => ViewImage::route('/{record}'),
        ];
    }
}
