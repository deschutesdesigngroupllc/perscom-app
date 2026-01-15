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
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use UnitEnum;

class WebhookResource extends BaseResource
{
    protected static ?string $model = Webhook::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static string|UnitEnum|null $navigationGroup = 'Integrations';

    protected static ?int $navigationSort = 9;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Text::make('tip')
                    ->visibleOn('create')
                    ->columnSpanFull()
                    ->content(fn (): HtmlString => new HtmlString(
                        '<div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">'.
                        '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-warning shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" /></svg>'.
                        '<span><strong>Did you know?</strong> You can trigger this webhook automatically using <a href="'.AutomationResource::getUrl('create').'" class="font-medium text-primary-600 hover:underline dark:text-primary-400">Automations</a>.</span>'.
                        '</div>'
                    )),
                Tabs::make()
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make('Webhook')
                            ->icon(Heroicon::OutlinedGlobeAlt)
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
                                    ->helperText('The events that will trigger the webhook. Leave empty if using only with automations.')
                                    ->multiple()
                                    ->options(fn () => collect(WebhookEvent::cases())->mapWithKeys(fn (WebhookEvent $event): array => [$event->value => $event->value])->toArray()),
                                TextInput::make('secret')
                                    ->revealable()
                                    ->default(Str::random(32))
                                    ->helperText('A secret key that will be used to sign the data.')
                                    ->password()
                                    ->required()
                                    ->maxLength(255),
                            ]),
                    ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Webhook')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                TextEntry::make('url')
                                    ->label('URL')
                                    ->copyable(),
                                TextEntry::make('description')
                                    ->visible(fn ($state): bool => filled($state))
                                    ->html(),
                                TextEntry::make('method')
                                    ->badge()
                                    ->color('gray'),
                                TextEntry::make('events')
                                    ->placeholder('No Events')
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
            ->emptyStateIcon(Heroicon::OutlinedGlobeAlt)
            ->emptyStateDescription('Create your first webhook to start sending real-time notifications.')
            ->columns([
                TextColumn::make('url')
                    ->sortable(),
                TextColumn::make('description')
                    ->placeholder('No Description')
                    ->formatStateUsing(fn ($state) => Str::limit($state))
                    ->sortable()
                    ->html()
                    ->wrap(),
                TextColumn::make('method')
                    ->badge()
                    ->sortable()
                    ->color('gray'),
                TextColumn::make('events')
                    ->placeholder('No Events')
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

    /**
     * @return array<string, PageRegistration>
     */
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
