<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MailResource\Pages;
use App\Models\Mail;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
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
                                    ->required(),
                                Forms\Components\Checkbox::make('send_now')
                                    ->label('Send Now')
                                    ->default(true)
                                    ->live()
                                    ->helperText('If checked, the email will be sent immediately.'),
                                Forms\Components\DateTimePicker::make('send_at')
                                    ->label('Send At')
                                    ->default(now()->addMinutes(5))
                                    ->visible(fn (Forms\Get $get): bool => ! $get('send_now'))
                                    ->requiredIf('send_now', false)
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
                    ->searchable()
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
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMails::route('/'),
            'create' => Pages\CreateMail::route('/create'),
            'edit' => Pages\EditMail::route('/{record}/edit'),
        ];
    }

    /**
     * @param  Mail  $record
     */
    public static function canEdit(Model $record): bool
    {
        if (filled($record->sent_at)) {
            return false;
        }

        return parent::canEdit($record);
    }
}
