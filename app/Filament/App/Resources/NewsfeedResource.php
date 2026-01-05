<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\NewsfeedResource\Pages\CreateNewsfeed;
use App\Filament\App\Resources\NewsfeedResource\Pages\EditNewsfeed;
use App\Filament\App\Resources\NewsfeedResource\Pages\ListNewsfeeds;
use App\Filament\App\Resources\NewsfeedResource\Pages\ViewNewsfeed;
use App\Models\Newsfeed;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use UnitEnum;

class NewsfeedResource extends BaseResource
{
    protected static ?string $model = Newsfeed::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $label = 'newsfeed item';

    protected static ?string $navigationLabel = 'Newsfeed';

    protected static string|UnitEnum|null $navigationGroup = 'Communications';

    protected static ?string $slug = 'newsfeed';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->tabs([
                        Tabs\Tab::make('Item')
                            ->icon(Heroicon::OutlinedNewspaper)
                            ->columns()
                            ->schema([
                                TextInput::make('headline')
                                    ->columnSpanFull()
                                    ->helperText("The newsfeed item's headline.")
                                    ->required()
                                    ->maxValue(255),
                                RichEditor::make('text')
                                    ->extraInputAttributes(['style' => 'min-height: 10rem;'])
                                    ->columnSpanFull()
                                    ->helperText("The newsfeed item's content.")
                                    ->required()
                                    ->maxLength(65535),
                                DateTimePicker::make('created_at')
                                    ->helperText('The date of the newsfeed item.')
                                    ->default(now())
                                    ->required(),
                                TextInput::make('causer_type')
                                    ->default(User::class)
                                    ->hidden(),
                                Select::make('causer_id')
                                    ->label('Author')
                                    ->required()
                                    ->default(Auth::user()->getAuthIdentifier())
                                    ->helperText('The author of the newsfeed item.')
                                    ->preload()
                                    ->options(User::query()->orderBy('name')->pluck('name', 'id')->all())
                                    ->searchable()
                                    ->createOptionForm(fn (Schema $form): Schema => UserResource::form($form)),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateIcon(Heroicon::OutlinedNewspaper)
            ->emptyStateDescription('There are no newsfeed items to view. Perform an action to get started.')
            ->columns([
                Split::make([
                    Stack::make([
                        TextColumn::make('headline')
                            ->toggleable(false)
                            ->weight(FontWeight::Bold),
                        TextColumn::make('text')
                            ->toggleable(false)
                            ->html()
                            ->wrap()
                            ->formatStateUsing(fn ($state) => Str::limit($state, 250)),
                        TextColumn::make('created_at')
                            ->toggleable(false)
                            ->weight(FontWeight::Light)
                            ->color(Color::Gray),
                    ]),
                ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextEntry::make('headline')
                    ->hiddenLabel()
                    ->size(TextSize::Large)
                    ->extraAttributes([
                        'class' => 'font-bold',
                    ]),
                TextEntry::make('created_at')
                    ->hiddenLabel()
                    ->size(TextSize::Small)
                    ->extraAttributes([
                        'class' => '-mt-6 text-gray-700!',
                    ]),
                TextEntry::make('text')
                    ->hiddenLabel()
                    ->html(),
            ]);
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListNewsfeeds::route('/'),
            'create' => CreateNewsfeed::route('/create'),
            'view' => ViewNewsfeed::route('/{record}'),
            'edit' => EditNewsfeed::route('/{record}/edit'),
        ];
    }
}
