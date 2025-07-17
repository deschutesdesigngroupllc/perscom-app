<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Actions\Batches\SendTenantMassEmails;
use App\Filament\Admin\Resources\MailResource\Pages;
use App\Models\Mail;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Model;

class MailResource extends Resource
{
    protected static ?string $model = Mail::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Communications';

    protected static ?string $navigationLabel = 'Mail';

    protected static ?string $label = 'email';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Email')
                            ->icon('heroicon-o-envelope')
                            ->schema([
                                Forms\Components\Select::make('recipients')
                                    ->helperText('Leave blank to send to all tenants.')
                                    ->multiple()
                                    ->searchable()
                                    ->required()
                                    ->nullable()
                                    ->options(Tenant::query()->orderBy('name')->pluck('name', 'id')),
                                Forms\Components\TextInput::make('subject')
                                    ->required()
                                    ->maxLength(255),
                                TiptapEditor::make('content')
                                    ->extraInputAttributes(['style' => 'min-height: 20rem;'])
                                    ->required(),
                                Forms\Components\Checkbox::make('send_now')
                                    ->label('Send Now')
                                    ->default(true)
                                    ->live()
                                    ->helperText('If checked, the email will be sent immediately.'),
                                Forms\Components\DateTimePicker::make('send_at')
                                    ->hintActions([
                                        Forms\Components\Actions\Action::make('now')->action(fn (Forms\Set $set): mixed => $set('send_at', now()->toDateTimeString())),
                                        Forms\Components\Actions\Action::make('clear')->action(fn (Forms\Set $set): mixed => $set('send_at', null)),
                                    ])
                                    ->label('Send At')
                                    ->visible(fn (Forms\Get $get): bool => ! $get('send_now'))
                                    ->helperText('Set the time to send the email.'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject')
                    ->searchable(['subject', 'content'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('sent_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('send_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->toggleable()
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('retry')
                    ->successNotificationTitle('The email has been queued for sending.')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (Mail $record, Tables\Actions\Action $action): void {
                        SendTenantMassEmails::handle($record);
                        $action->success();
                    })
                    ->visible(fn (Mail $record): bool => (
                        ($record->send_now && blank($record->send_at)) || (filled($record->send_at) && $record->send_at->isPast()))
                        && blank($record->sent_at)
                    ),
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
            'index' => Pages\ListMails::route('/'),
            'create' => Pages\CreateMail::route('/create'),
            'edit' => Pages\EditMail::route('/{record}/edit'),
            'view' => Pages\ViewMail::route('/{record}'),
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
