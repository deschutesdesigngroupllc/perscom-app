<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Actions\Batches\SendTenantMassEmails;
use App\Filament\Admin\Resources\MailResource\Pages\CreateMail;
use App\Filament\Admin\Resources\MailResource\Pages\EditMail;
use App\Filament\Admin\Resources\MailResource\Pages\ListMails;
use App\Filament\Admin\Resources\MailResource\Pages\ViewMail;
use App\Models\Mail;
use App\Models\Tenant;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class MailResource extends Resource
{
    protected static ?string $model = Mail::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static string|UnitEnum|null $navigationGroup = 'Communications';

    protected static ?string $navigationLabel = 'Mail';

    protected static ?string $label = 'email';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Email')
                            ->icon('heroicon-o-envelope')
                            ->schema([
                                Select::make('recipients')
                                    ->helperText('Leave blank to send to all tenants.')
                                    ->multiple()
                                    ->searchable()
                                    ->required()
                                    ->nullable()
                                    ->options(Tenant::query()->orderBy('name')->pluck('name', 'id')),
                                TextInput::make('subject')
                                    ->required()
                                    ->maxLength(255),
                                TiptapEditor::make('content')
                                    ->extraInputAttributes(['style' => 'min-height: 20rem;'])
                                    ->required(),
                                Checkbox::make('send_now')
                                    ->label('Send Now')
                                    ->default(true)
                                    ->live()
                                    ->helperText('If checked, the email will be sent immediately.'),
                                DateTimePicker::make('send_at')
                                    ->hintActions([
                                        Action::make('now')->action(fn (Set $set): mixed => $set('send_at', now()->toDateTimeString())),
                                        Action::make('clear')->action(fn (Set $set): mixed => $set('send_at', null)),
                                    ])
                                    ->label('Send At')
                                    ->visible(fn (Get $get): bool => ! $get('send_now'))
                                    ->helperText('Set the time to send the email.'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject')
                    ->searchable(['subject', 'content'])
                    ->sortable(),
                TextColumn::make('sent_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('send_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->toggleable()
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('retry')
                    ->successNotificationTitle('The email has been queued for sending.')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (Mail $record, Action $action): void {
                        SendTenantMassEmails::handle($record);
                        $action->success();
                    })
                    ->visible(fn (Mail $record): bool => (
                        ($record->send_now && blank($record->send_at)) || (filled($record->send_at) && $record->send_at->isPast()))
                        && blank($record->sent_at)
                    ),
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
                TextEntry::make('content')
                    ->hiddenLabel()
                    ->html()
                    ->prose(),
                TextEntry::make('send_at')
                    ->label('Send At')
                    ->dateTime()
                    ->visible(fn ($state, Mail $record): bool => filled($state) && ! $record->sent_at),
                TextEntry::make('sent_at')
                    ->label('Sent At')
                    ->dateTime()
                    ->visible(fn ($state): bool => filled($state)),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMails::route('/'),
            'create' => CreateMail::route('/create'),
            'edit' => EditMail::route('/{record}/edit'),
            'view' => ViewMail::route('/{record}'),
        ];
    }

    /**
     * @param  Mail  $record
     */
    public static function canEdit(Model $record): bool
    {
        if (filled($record->sent_at) || $record->send_now) {
            return false;
        }

        return parent::canEdit($record);
    }
}
