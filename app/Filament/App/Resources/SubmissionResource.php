<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Features\ExportDataFeature;
use App\Filament\App\Resources\SubmissionResource\Pages;
use App\Filament\App\Resources\SubmissionResource\RelationManagers\CommentsRelationManager;
use App\Filament\App\Resources\SubmissionResource\RelationManagers\StatusesRelationManager;
use App\Filament\Exports\SubmissionExporter;
use App\Models\Submission;
use App\Rules\FieldDataRule;
use App\Services\UserSettingsService;
use App\Settings\OrganizationSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Laravel\Pennant\Feature;

class SubmissionResource extends BaseResource
{
    protected static ?string $model = Submission::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-plus';

    protected static ?string $navigationGroup = 'Forms';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('form_id')
                    ->preload()
                    ->relationship(name: 'form', titleAttribute: 'name')
                    ->searchable()
                    ->createOptionForm(fn ($form) => FormResource::form($form)),
                Forms\Components\Select::make('user_id')
                    ->preload()
                    ->relationship(name: 'user', titleAttribute: 'name')
                    ->searchable()
                    ->createOptionForm(fn ($form) => UserResource::form($form)),
                Forms\Components\KeyValue::make('data')
                    ->columnSpanFull()
                    ->helperText('The submission data.')
                    ->keyLabel('Field Slug')
                    ->visibleOn('edit')
                    ->rule(new FieldDataRule),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        $entries = [];

        /** @var Submission $submission */
        $submission = $infolist->getRecord();

        if ($submission->form?->fields->isNotEmpty()) {
            foreach ($submission->form->fields as $field) {
                /** @var Entry $class */
                $class = $field->type->getFilamentEntry();

                $filamentEntry = $class::make($field->key)
                    ->label($field->name)
                    ->hidden($field->hidden);

                if ($field->type->value === 'boolean' && $filamentEntry instanceof TextEntry) {
                    $filamentEntry = $filamentEntry
                        ->badge()
                        ->formatStateUsing(fn ($state) => match ($state) {
                            '1', 1, true => 'True',
                            default => 'False'
                        })
                        ->color(fn ($state) => match ($state) {
                            '1', 1, true => 'success',
                            default => 'danger'
                        });
                }

                if ($field->type->value === 'date' && $filamentEntry instanceof TextEntry) {
                    $filamentEntry = $filamentEntry->date();
                }

                if ($field->type->value === 'datetime-local' && $filamentEntry instanceof TextEntry) {
                    $filamentEntry = $filamentEntry
                        ->timezone(UserSettingsService::get('timezone', function () {
                            /** @var OrganizationSettings $settings */
                            $settings = app(OrganizationSettings::class);

                            return $settings->timezone ?? config('app.timezone');
                        }))
                        ->dateTime();
                }

                $entries[] = $filamentEntry;
            }
        }

        return $infolist
            ->schema([
                Tabs::make()
                    ->tabs([
                        Tabs\Tab::make('Submission')
                            ->badge(fn (?Submission $record) => $record->status->name ?? 'Status')
                            ->badgeColor(fn (?Submission $record) => Color::hex($record->status->color ?? '#2563eb'))
                            ->icon('heroicon-o-folder-plus')
                            ->schema([
                                TextEntry::make('user.name'),
                                TextEntry::make('form.name'),
                            ]),
                        Tabs\Tab::make('Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextEntry::make('created_at'),
                                TextEntry::make('updated_at'),
                                TextEntry::make('deleted_at'),
                            ]),
                        Tabs\Tab::make('')
                            ->icon('heroicon-o-pencil-square')
                            ->label(fn (?Submission $record) => $record->form->name ?? 'Form')
                            ->schema($entries),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('form.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status.name')
                    ->badge()
                    ->color(fn (?Submission $record) => Color::hex($record->status->color ?? '#2563eb'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->sortable(),
            ])
            ->groups(['user.name'])
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->preload()
                    ->multiple(),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ExportAction::make()
                        ->visible(Feature::active(ExportDataFeature::class))
                        ->exporter(SubmissionExporter::class),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CommentsRelationManager::make(),
            StatusesRelationManager::make(),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubmissions::route('/'),
            'create' => Pages\CreateSubmission::route('/create'),
            'edit' => Pages\EditSubmission::route('/{record}/edit'),
            'view' => Pages\ViewSubmission::route('/{record}'),
        ];
    }
}
