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
use App\Models\Submission;
use App\Rules\FieldDataRule;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

class SubmissionResource extends BaseResource
{
    protected static ?string $model = Submission::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-folder-plus';

    protected static string|UnitEnum|null $navigationGroup = 'Forms';

    protected static ?int $navigationSort = 2;

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
                Select::make('form_id')
                    ->preload()
                    ->relationship(name: 'form', titleAttribute: 'name')
                    ->searchable()
                    ->createOptionForm(fn ($form): Schema => FormResource::form($form)),
                Select::make('user_id')
                    ->preload()
                    ->relationship(name: 'user', titleAttribute: 'name')
                    ->searchable()
                    ->createOptionForm(fn ($form): Schema => UserResource::form($form)),
                KeyValue::make('data')
                    ->columnSpanFull()
                    ->helperText('The submission data.')
                    ->keyLabel('Field Slug')
                    ->visibleOn('edit')
                    ->rule(new FieldDataRule),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Submission')
                            ->badge(fn (?Submission $record) => $record->status->name ?? 'Status')
                            ->badgeColor(fn (?Submission $record): array => Color::generateV3Palette($record->status->color ?? '#2563eb'))
                            ->icon('heroicon-o-folder-plus')
                            ->schema([
                                TextEntry::make('user.name'),
                                TextEntry::make('form.name'),
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
                        Tab::make('')
                            ->icon('heroicon-o-pencil-square')
                            ->label(fn (?Submission $record) => $record->form->name ?? 'Form')
                            ->schema([
                                ViewEntry::make('form')
                                    ->view('models.submission'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('form.name')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('status.name')
                    ->badge()
                    ->color(fn (?Submission $record): array => Color::generateV3Palette($record->status->color ?? '#2563eb'))
                    ->sortable(),
                TextColumn::make('read_at')
                    ->label('Read')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->groups(['user.name'])
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
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CommentsRelationManager::class,
            StatusesRelationManager::class,
        ];
    }

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
