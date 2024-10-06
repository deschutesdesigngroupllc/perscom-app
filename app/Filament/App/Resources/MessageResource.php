<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\MessageResource\Pages;
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
        $message = Forms\Components\RichEditor::make('message')
            ->helperText('Enter the message you would like to send.')
            ->required()
            ->hiddenLabel();

        $channels = Forms\Components\CheckboxList::make('channels')
            ->hiddenLabel()
            ->options([
                'sms' => 'SMS',
                'discord' => 'Discord',
                'mail' => 'Email',
                'broadcast' => 'Live',
                'database' => 'Dashboard',
            ])
            ->descriptions([
                'sms' => 'The this message straight to the user\'s cell phone.',
                'discord' => 'Send the message to your configured discord channel.',
                'mail' => 'Send this message as an email.',
                'broadcast' => 'Send this message as an instant notification to the user\'s dashboard.',
                'database' => 'Store this message in the user\'s database so they can review it later.',
            ]);

        if ($form->getOperation() === 'create') {
            return $form->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Message')
                        ->schema([
                            $message,
                        ]),
                    Forms\Components\Wizard\Step::make('Channels')
                        ->schema([
                            $channels,
                        ]),
                    Forms\Components\Wizard\Step::make('Details'),
                ])->columnSpanFull(),
            ]);
        }

        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Message')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->schema([
                                $message,
                            ]),
                        Forms\Components\Tabs\Tab::make('Channels')
                            ->icon('heroicon-o-queue-list')
                            ->schema([
                                $channels,
                            ]),
                        Forms\Components\Tabs\Tab::make('Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\DateTimePicker::make('send_at')
                                    ->nullable()
                                    ->helperText('Set a time and date to send later. Leave blank to send immediately.'),
                                Forms\Components\Toggle::make('repeats')
                                    ->helperText('Enable to send the message on a recurring schedule.'),

                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
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
