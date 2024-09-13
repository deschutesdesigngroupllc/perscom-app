<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\NewsfeedResource\Pages;
use App\Models\Newsfeed;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NewsfeedResource extends BaseResource
{
    protected static ?string $model = Newsfeed::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $modelLabel = 'item';

    protected static ?string $pluralModelLabel = 'Newsfeed';

    protected static ?string $navigationLabel = 'Newsfeed';

    protected static ?string $navigationGroup = 'Communications';

    protected static ?string $slug = 'newsfeed';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('headline')
                    ->columnSpanFull()
                    ->helperText('The newsfeed item\'s headline.')
                    ->required()
                    ->maxValue(255),
                Forms\Components\RichEditor::make('text')
                    ->columnSpanFull()
                    ->helperText('The newsfeed item\'s content.')
                    ->required()
                    ->maxLength(65535),
                Forms\Components\DateTimePicker::make('created_at')
                    ->helperText('The date of the newsfeed item.')
                    ->default(now())
                    ->required(),
                Forms\Components\TextInput::make('causer_type')
                    ->default(User::class)
                    ->hidden(),
                Forms\Components\Select::make('causer_id')
                    ->label('Author')
                    ->required()
                    ->default(Auth::user()->getAuthIdentifier())
                    ->helperText('The author of the newsfeed item.')
                    ->preload()
                    ->options(User::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->createOptionForm(fn ($form) => UserResource::form($form)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('headline'),
                Tables\Columns\TextColumn::make('text')
                    ->html()
                    ->wrap()
                    ->formatStateUsing(fn ($state) => Str::limit($state)),
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('Author'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(1)
            ->schema([
                TextEntry::make('headline')
                    ->hiddenLabel()
                    ->size(TextEntry\TextEntrySize::Large)
                    ->extraAttributes([
                        'class' => 'font-bold',
                    ]),
                TextEntry::make('created_at')
                    ->hiddenLabel()
                    ->size(TextEntry\TextEntrySize::Small)
                    ->extraAttributes([
                        'class' => '-mt-6 !text-gray-700',
                    ])
                    ->dateTime(),
                TextEntry::make('text')
                    ->hiddenLabel()
                    ->html(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewsfeeds::route('/'),
            'create' => Pages\CreateNewsfeed::route('/create'),
            'view' => Pages\ViewNewsfeed::route('/{record}'),
            'edit' => Pages\EditNewsfeed::route('/{record}/edit'),
        ];
    }
}
