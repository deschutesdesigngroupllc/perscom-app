<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\WebhookResource\Pages\CreateWebhook;
use App\Filament\App\Resources\WebhookResource\Pages\EditWebhook;
use App\Filament\App\Resources\WebhookResource\Pages\ListWebhooks;
use App\Filament\App\Resources\WebhookResource\Pages\ViewWebhook;
use App\Models\Enums\WebhookEvent;
use App\Models\Enums\WebhookMethod;
use App\Models\Webhook;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class WebhookResource extends BaseResource
{
    protected static ?string $model = Webhook::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static string|UnitEnum|null $navigationGroup = 'Integrations';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Webhook Information')
                    ->schema([
                        TextInput::make('url')
                            ->helperText('The URL that the data will be sent to.')
                            ->label('URL')
                            ->url()
                            ->required()
                            ->maxLength(255),
                        RichEditor::make('description')
                            ->extraInputAttributes(['style' => 'min-height: 10rem;'])
                            ->helperText('A brief description of the webhook.')
                            ->nullable()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Select::make('method')
                            ->helperText('The HTTP method that will be used to send the data.')
                            ->options(WebhookMethod::class)
                            ->required()
                            ->default(WebhookMethod::POST),
                        Select::make('events')
                            ->helperText('The events that will trigger the webhook.')
                            ->multiple()
                            ->options(fn () => collect(WebhookEvent::cases())->mapWithKeys(fn (WebhookEvent $event) => [$event->value => $event->value])->toArray())
                            ->required(),
                        TextInput::make('secret')
                            ->revealable()
                            ->default(Str::random(32))
                            ->helperText('A secret key that will be used to sign the data.')
                            ->password()
                            ->required()
                            ->maxLength(255),
                    ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()
                ->columnSpanFull()
                ->tabs([
                    Tab::make('Webhook')
                        ->icon('heroicon-o-globe-alt')
                        ->schema([
                            TextEntry::make('url')
                                ->label('URL')
                                ->copyable(),
                            TextEntry::make('description')
                                ->visible(fn ($state) => filled($state))
                                ->html(),
                            TextEntry::make('method')
                                ->badge()
                                ->color('gray'),
                            TextEntry::make('events')
                                ->listWithLineBreaks()
                                ->limitList()
                                ->expandableLimitedList()
                                ->badge(),
                            TextEntry::make('secret')
                                ->copyable(),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateDescription('Create your first webhook to start sending real-time notifications.')
            ->columns([
                TextColumn::make('url')
                    ->sortable(),
                TextColumn::make('description')
                    ->formatStateUsing(fn ($state) => Str::limit($state))
                    ->sortable()
                    ->html()
                    ->wrap(),
                TextColumn::make('method')
                    ->badge()
                    ->sortable()
                    ->color('gray'),
                TextColumn::make('events')
                    ->listWithLineBreaks()
                    ->expandableLimitedList()
                    ->limitList()
                    ->sortable()
                    ->badge(),
                TextColumn::make('created_at')
                    ->toggleable(false)
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

    public static function getPages(): array
    {
        return [
            'index' => ListWebhooks::route('/'),
            'create' => CreateWebhook::route('/create'),
            'edit' => EditWebhook::route('/{record}/edit'),
            'view' => ViewWebhook::route('/{record}'),
        ];
    }
}
