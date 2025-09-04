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
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use UnitEnum;

class NewsfeedResource extends BaseResource
{
    protected static ?string $model = Newsfeed::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $modelLabel = 'item';

    protected static ?string $pluralModelLabel = 'Newsfeed';

    protected static ?string $navigationLabel = 'Newsfeed';

    protected static string|UnitEnum|null $navigationGroup = 'Communications';

    protected static ?string $slug = 'newsfeed';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('headline')
                    ->columnSpanFull()
                    ->helperText('The newsfeed item\'s headline.')
                    ->required()
                    ->maxValue(255),
                RichEditor::make('text')
                    ->columnSpanFull()
                    ->helperText('The newsfeed item\'s content.')
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
                    ->createOptionForm(fn ($form): Schema => UserResource::form($form)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('headline'),
                TextColumn::make('text')
                    ->html()
                    ->wrap()
                    ->formatStateUsing(fn ($state) => Str::limit($state)),
                TextColumn::make('causer.name')
                    ->label('Author'),
                TextColumn::make('created_at')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->sortable(),
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
                        'class' => '-mt-6 !text-gray-700',
                    ]),
                TextEntry::make('text')
                    ->hiddenLabel()
                    ->html(),
            ]);
    }

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
