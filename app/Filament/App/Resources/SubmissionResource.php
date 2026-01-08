<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\SubmissionResource\Pages\CreateSubmission;
use App\Filament\App\Resources\SubmissionResource\Pages\EditSubmission;
use App\Filament\App\Resources\SubmissionResource\Pages\ListSubmissions;
use App\Filament\App\Resources\SubmissionResource\Pages\ViewSubmission;
use App\Filament\App\Resources\SubmissionResource\RelationManagers\CommentsRelationManager;
use App\Filament\App\Resources\SubmissionResource\RelationManagers\StatusesRelationManager;
use App\Filament\Exports\SubmissionExporter;
use App\Models\Form;
use App\Models\Submission;
use App\Traits\Filament\BuildsCustomFieldComponents;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use UnitEnum;

class SubmissionResource extends BaseResource
{
    use BuildsCustomFieldComponents;

    protected static ?string $model = Submission::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-folder-plus';

    protected static string|UnitEnum|null $navigationGroup = 'Forms';

    protected static ?int $navigationSort = 4;

    public static function getNavigationBadge(): ?string
    {
        $count = Submission::query()
            ->unread()
            ->count();

        return $count > 0
            ? (string) $count
            : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->persistTabInQueryString('submission-tab')
                    ->tabs([
                        Tab::make('Submission')
                            ->badge(fn (?Submission $record) => $record->status->name ?? null)
                            ->badgeColor(fn (?Submission $record): array => Color::generateV3Palette($record->status->color ?? '#2563eb'))
                            ->icon(Heroicon::OutlinedFolderPlus)
                            ->schema([
                                Select::make('form_id')
                                    ->live()
                                    ->preload()
                                    ->relationship(name: 'form', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn (Schema $form): Schema => FormResource::form($form)),
                                Select::make('user_id')
                                    ->preload()
                                    ->relationship(name: 'user', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn (Schema $form): Schema => UserResource::form($form)),
                            ]),
                    ]),
                Tabs::make()
                    ->columnSpanFull()
                    ->persistTabInQueryString('form-tab')
                    ->tabs([
                        Tab::make('')
                            ->icon('heroicon-o-pencil-square')
                            ->label(fn (Get $get) => Form::find($get('form_id'))->name ?? 'Form')
                            ->schema(fn (Get $get): array => SubmissionResource::buildCustomFieldInputs(collect(Form::find($get('form_id'))?->fields))),
                    ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->persistTabInQueryString('submission-tab')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Submission')
                            ->badge(fn (?Submission $record) => $record->status->name ?? null)
                            ->badgeColor(fn (?Submission $record): array => Color::generateV3Palette($record->status->color ?? '#2563eb'))
                            ->icon('heroicon-o-folder-plus')
                            ->schema([
                                TextEntry::make('user.name'),
                                TextEntry::make('form.name')
                                    ->label('Form'),
                            ]),
                        Tab::make('Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextEntry::make('read_at')
                                    ->dateTime()
                                    ->label('Read'),
                                TextEntry::make('created_at'),
                                TextEntry::make('updated_at'),
                            ]),
                    ]),
                Tabs::make()
                    ->columnSpanFull()
                    ->persistTabInQueryString('form-tab')
                    ->tabs([
                        Tab::make('')
                            ->icon('heroicon-o-pencil-square')
                            ->label(fn (Submission $record) => $record->form->name ?? 'Form')
                            ->schema(fn (Submission $record): array => SubmissionResource::buildCustomFieldEntries($record->form->fields)),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateIcon(Heroicon::OutlinedFolderPlus)
            ->emptyStateDescription('There are no submissions to view. Create one to get started.')
            ->columns([
                TextColumn::make('form.name')
                    ->weight(fn (Submission $record): ?FontWeight => $record->read_at ? null : FontWeight::Bold)
                    ->icon(fn (Submission $record): ?Heroicon => $record->read_at ? null : Heroicon::OutlinedPlus)
                    ->iconPosition(IconPosition::Before)
                    ->searchable(),
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('status.name')
                    ->placeholder('No Status')
                    ->badge()
                    ->color(fn (?Submission $record): array => Color::generateV3Palette($record->status->color ?? '#2563eb'))
                    ->sortable(),
                TextColumn::make('read_at')
                    ->placeholder('Unread')
                    ->label('Read')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->groups([
                Group::make('form.categoryPivot.category_id')
                    ->label('Category')
                    ->getTitleFromRecordUsing(fn (Submission $record) => $record->form?->categoryPivot?->category?->name),
            ])
            ->filters([
                TernaryFilter::make('read_at')
                    ->label('Read')
                    ->nullable(),
                SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->preload()
                    ->multiple(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                ExportBulkAction::make()
                    ->exporter(SubmissionExporter::class)
                    ->icon('heroicon-o-document-arrow-down'),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultGroup('form.categoryPivot.category_id');
    }

    public static function getRelations(): array
    {
        return [
            CommentsRelationManager::class,
            StatusesRelationManager::class,
        ];
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListSubmissions::route('/'),
            'create' => CreateSubmission::route('/create'),
            'edit' => EditSubmission::route('/{record}/edit'),
            'view' => ViewSubmission::route('/{record}'),
        ];
    }
}
