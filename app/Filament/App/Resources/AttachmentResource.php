<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\AttachmentResource\Pages;
use App\Models\AssignmentRecord;
use App\Models\Attachment;
use App\Models\AwardRecord;
use App\Models\CombatRecord;
use App\Models\Event;
use App\Models\QualificationRecord;
use App\Models\RankRecord;
use App\Models\ServiceRecord;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttachmentResource extends BaseResource
{
    protected static ?string $model = Attachment::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-clip';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Attachment')
                            ->icon('heroicon-o-paper-clip')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->helperText('The name of the attachment.')
                                    ->maxLength(255),
                                Forms\Components\FileUpload::make('path')
                                    ->required()
                                    ->label('File')
                                    ->previewable()
                                    ->openable()
                                    ->downloadable()
                                    ->visibility('public')
                                    ->storeFileNamesIn('filename')
                                    ->disk('s3'),
                            ]),
                        Forms\Components\Tabs\Tab::make('Resource')
                            ->icon('heroicon-o-document')
                            ->schema([
                                Forms\Components\MorphToSelect::make('model')
                                    ->preload()
                                    ->hiddenLabel()
                                    ->types([
                                        Forms\Components\MorphToSelect\Type::make(Task::class)
                                            ->titleAttribute('title'),
                                        Forms\Components\MorphToSelect\Type::make(Event::class)
                                            ->titleAttribute('name'),
                                        Forms\Components\MorphToSelect\Type::make(AssignmentRecord::class)
                                            ->titleAttribute('id')
                                            ->getOptionLabelFromRecordUsing(fn (AssignmentRecord $record) => $record->getLabel()),
                                        Forms\Components\MorphToSelect\Type::make(AwardRecord::class)
                                            ->titleAttribute('id')
                                            ->getOptionLabelFromRecordUsing(fn (AwardRecord $record) => $record->getLabel()),
                                        Forms\Components\MorphToSelect\Type::make(CombatRecord::class)
                                            ->titleAttribute('id')
                                            ->getOptionLabelFromRecordUsing(fn (CombatRecord $record) => $record->getLabel()),
                                        Forms\Components\MorphToSelect\Type::make(QualificationRecord::class)
                                            ->titleAttribute('id')
                                            ->getOptionLabelFromRecordUsing(fn (QualificationRecord $record) => $record->getLabel()),
                                        Forms\Components\MorphToSelect\Type::make(RankRecord::class)
                                            ->titleAttribute('id')
                                            ->getOptionLabelFromRecordUsing(fn (RankRecord $record) => $record->getLabel()),
                                        Forms\Components\MorphToSelect\Type::make(ServiceRecord::class)
                                            ->titleAttribute('id')
                                            ->getOptionLabelFromRecordUsing(fn (ServiceRecord $record) => $record->getLabel()),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Attachment')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('attachment_url')
                                    ->label('URL')
                                    ->url(fn ($state) => $state)
                                    ->openUrlInNewTab()
                                    ->copyable(),
                                ImageEntry::make('path')
                                    ->label('Attachment')
                                    ->disk('s3'),
                            ]),
                        Tabs\Tab::make('Resource')
                            ->icon('heroicon-o-document')
                            ->schema([
                                TextEntry::make('model.label')
                                    ->label('Resource')
                                    ->badge()
                                    ->openUrlInNewTab()
                                    ->icon('heroicon-o-arrow-top-right-on-square')
                                    ->iconPosition(IconPosition::After)
                                    ->url(fn (Attachment $record) => $record->model_url),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->emptyStateDescription('Create an attachment to get started.')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('filename')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('attachment_url')
                    ->label('URL')
                    ->url(fn ($state) => $state)
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->iconPosition(IconPosition::After)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('model.label')
                    ->label('Resource')
                    ->badge()
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->iconPosition(IconPosition::After)
                    ->url(fn (Attachment $record) => $record->model_url),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->icon('heroicon-o-cloud-arrow-down')
                    ->color('gray')
                    ->url(fn (?Attachment $record) => $record->attachment_url)
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function relationManagerTable(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->emptyStateDescription('Create an attachment to get started.')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('filename')
                    ->sortable(),
                Tables\Columns\TextColumn::make('attachment_url')
                    ->label('URL')
                    ->url(fn ($state) => $state)
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->iconPosition(IconPosition::After)
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->icon('heroicon-o-cloud-arrow-down')
                    ->color('gray')
                    ->url(fn (?Attachment $record) => $record->attachment_url)
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttachments::route('/'),
            'create' => Pages\CreateAttachment::route('/create'),
            'edit' => Pages\EditAttachment::route('/{record}/edit'),
            'view' => Pages\ViewAttachment::route('/{record}'),
        ];
    }
}
