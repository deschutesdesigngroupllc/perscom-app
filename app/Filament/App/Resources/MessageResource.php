<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\MessageResource\Pages;
use App\Forms\Components\Schedule;
use App\Models\Enums\NotificationChannel;
use App\Models\Message;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MessageResource extends BaseResource
{
    protected static ?string $model = Message::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Communications';

    public static function form(Form $form): Form
    {
        $message = [
            Forms\Components\RichEditor::make('message')
                ->helperText('Enter the message you would like to send.')
                ->required()
                ->hiddenLabel(),
        ];

        $channels = [
            Forms\Components\CheckboxList::make('channels')
                ->required()
                ->hiddenLabel()
                ->bulkToggleable()
                ->options(NotificationChannel::class),
        ];

        $details = [
            Forms\Components\DateTimePicker::make('send_at')
                ->default(now())
                ->columnSpanFull()
                ->helperText('Set a time to send the message in the future. Leave blank to send now.')
                ->hidden(fn (Forms\Get $get) => $get('repeats')),
            Forms\Components\Toggle::make('repeats')
                ->default(true)
                ->live()
                ->helperText('Enable to send the message on a recurring schedule.'),
        ];

        $schedule = [
            Schedule::make()
                ->visible(fn (Forms\Get $get) => $get('repeats')),
        ];

        if ($form->getOperation() === 'create') {
            return $form->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Message')
                        ->schema($message),
                    Forms\Components\Wizard\Step::make('Channels')
                        ->schema($channels),
                    Forms\Components\Wizard\Step::make('Details')
                        ->schema($details),
                    Forms\Components\Wizard\Step::make('Schedule')
                        ->schema($schedule),
                ])->persistStepInQueryString()->columnSpanFull(),
            ]);
        }

        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Message')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->schema($message),
                        Forms\Components\Tabs\Tab::make('Channels')
                            ->icon('heroicon-o-queue-list')
                            ->schema($channels),
                        Forms\Components\Tabs\Tab::make('Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema($details),
                        Forms\Components\Tabs\Tab::make('Schedule')
                            ->visible(fn (Forms\Get $get) => $get('repeats'))
                            ->icon('heroicon-o-arrow-path')
                            ->schema($schedule),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('message')
                    ->html()
                    ->wrap()
                    ->sortable()
                    ->icon(fn (Message $record) => $record->repeats ? 'heroicon-o-arrow-path' : null),
                Tables\Columns\TextColumn::make('channels')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('send_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sent_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('sent_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMessages::route('/'),
            'create' => Pages\CreateMessage::route('/create'),
            'edit' => Pages\EditMessage::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
