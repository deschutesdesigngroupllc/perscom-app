<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Settings\Pages;

use App\Features\BackupFeature;
use App\Filament\App\Clusters\Settings;
use App\Models\Backup;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Laravel\Pennant\Feature;

class Backups extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static string $view = 'filament.app.clusters.settings.pages.backups';

    protected static ?string $cluster = Settings::class;

    protected static ?int $navigationSort = 2;

    protected ?string $subheading = 'Your most recent account backups.';

    public static function canAccess(): bool
    {
        return parent::canAccess() && Feature::active(BackupFeature::class);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Backup::query())
            ->description('Download your latest account backups here.')
            ->emptyStateHeading('No current backups')
            ->emptyStateDescription('There are no current backups. Backups are taken automatically every night.')
            ->columns([
                TextColumn::make('name')
                    ->sortable(),
                TextColumn::make('size')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Action::make('download')
                    ->icon('heroicon-o-cloud-arrow-down')
                    ->openUrlInNewTab()
                    ->url(fn (Backup $record) => $record->url),
            ]);
    }
}
