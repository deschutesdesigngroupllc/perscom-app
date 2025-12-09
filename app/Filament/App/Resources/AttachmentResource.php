<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\AttachmentResource\Pages\CreateAttachment;
use App\Filament\App\Resources\AttachmentResource\Pages\EditAttachment;
use App\Filament\App\Resources\AttachmentResource\Pages\ListAttachments;
use App\Filament\App\Resources\AttachmentResource\Pages\ViewAttachment;
use App\Models\AssignmentRecord;
use App\Models\Attachment;
use App\Models\AwardRecord;
use App\Models\CombatRecord;
use App\Models\Event;
use App\Models\QualificationRecord;
use App\Models\RankRecord;
use App\Models\ServiceRecord;
use App\Models\Task;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class AttachmentResource extends BaseResource
{
    protected static ?string $model = Attachment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-paper-clip';

    protected static string|UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Attachment')
                            ->icon('heroicon-o-paper-clip')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->helperText('The name of the attachment.')
                                    ->maxLength(255),
                                FileUpload::make('path')
                                    ->required()
                                    ->label('File')
                                    ->previewable()
                                    ->openable()
                                    ->downloadable()
                                    ->visibility('public')
                                    ->storeFileNamesIn('filename'),
                            ]),
                        Tab::make('Resource')
                            ->icon('heroicon-o-document')
                            ->schema([
                                MorphToSelect::make('model')
                                    ->preload()
                                    ->hiddenLabel()
                                    ->types([
                                        Type::make(Task::class)
                                            ->titleAttribute('title'),
                                        Type::make(Event::class)
                                            ->titleAttribute('name'),
                                        Type::make(AssignmentRecord::class)
                                            ->titleAttribute('id')
                                            ->getOptionLabelFromRecordUsing(fn (AssignmentRecord $record): string => $record->getLabel()),
                                        Type::make(AwardRecord::class)
                                            ->titleAttribute('id')
                                            ->getOptionLabelFromRecordUsing(fn (AwardRecord $record): string => $record->getLabel()),
                                        Type::make(CombatRecord::class)
                                            ->titleAttribute('id')
                                            ->getOptionLabelFromRecordUsing(fn (CombatRecord $record): string => $record->getLabel()),
                                        Type::make(QualificationRecord::class)
                                            ->titleAttribute('id')
                                            ->getOptionLabelFromRecordUsing(fn (QualificationRecord $record): string => $record->getLabel()),
                                        Type::make(RankRecord::class)
                                            ->titleAttribute('id')
                                            ->getOptionLabelFromRecordUsing(fn (RankRecord $record): string => $record->getLabel()),
                                        Type::make(ServiceRecord::class)
                                            ->titleAttribute('id')
                                            ->getOptionLabelFromRecordUsing(fn (ServiceRecord $record): string => $record->getLabel()),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Attachment')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('attachment_url')
                                    ->label('URL')
                                    ->url(fn ($state) => $state)
                                    ->openUrlInNewTab()
                                    ->copyable(),
                                ImageEntry::make('path')
                                    ->label('Attachment'),
                            ]),
                        Tab::make('Resource')
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
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('filename')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('attachment_url')
                    ->label('URL')
                    ->url(fn ($state) => $state)
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->iconPosition(IconPosition::After)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('model.label')
                    ->label('Resource')
                    ->badge()
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->iconPosition(IconPosition::After)
                    ->url(fn (Attachment $record) => $record->model_url),
                TextColumn::make('created_at')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('download')
                    ->icon('heroicon-o-cloud-arrow-down')
                    ->color('gray')
                    ->url(fn (?Attachment $record) => $record->attachment_url)
                    ->openUrlInNewTab(),
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

    public static function relationManagerTable(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->description('The attachments that belong to this resource.')
            ->emptyStateDescription('Create an attachment to get started.')
            ->description('The attachments associated with this resource.')
            ->columns([
                TextColumn::make('name')
                    ->sortable(),
                TextColumn::make('filename')
                    ->sortable(),
                TextColumn::make('attachment_url')
                    ->label('URL')
                    ->url(fn ($state) => $state)
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->iconPosition(IconPosition::After)
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                Action::make('download')
                    ->icon('heroicon-o-cloud-arrow-down')
                    ->color('gray')
                    ->url(fn (?Attachment $record) => $record->attachment_url)
                    ->openUrlInNewTab(),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListAttachments::route('/'),
            'create' => CreateAttachment::route('/create'),
            'edit' => EditAttachment::route('/{record}/edit'),
            'view' => ViewAttachment::route('/{record}'),
        ];
    }
}
