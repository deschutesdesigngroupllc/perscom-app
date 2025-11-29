<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Settings\Pages;

use App\Filament\App\Clusters\Settings;
use App\Jobs\Tenant\BackupDatabase;
use App\Models\Backup;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use UnitEnum;

class Backups extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-circle-stack';

    protected static string|UnitEnum|null $navigationGroup = 'Dashboard';

    protected string $view = 'filament.app.clusters.settings.pages.backups';

    protected static ?string $cluster = Settings::class;

    protected static ?int $navigationSort = 2;

    protected ?string $subheading = 'Your most recent account backups.';

    public static function table(Table $table): Table
    {
        return $table
            ->query(Backup::query())
            ->emptyStateHeading('No current backups')
            ->emptyStateDescription('There are no current backups. Backups are taken automatically every night.')
            ->columns([
                TextColumn::make('name')
                    ->sortable(),
                TextColumn::make('size')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->toggleable(false)
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('download')
                    ->icon('heroicon-o-cloud-arrow-down')
                    ->openUrlInNewTab()
                    ->url(fn (Backup $record) => $record->url),
            ]);
    }

    /**
     * @return Action[]
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('backup_now')
                ->successNotificationTitle('The backup has been requested. Please check back later for the latest copy.')
                ->action(function (Action $action): void {
                    BackupDatabase::dispatch(
                        tenantKey: tenant()->getKey(),
                    );

                    $action->success();
                }),
        ];
    }
}
