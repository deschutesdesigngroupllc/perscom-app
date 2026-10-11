<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Data\Readiness\UnitReadinessData;
use App\Filament\App\Pages\Readiness\ExpirationsChart;
use App\Models\QualificationRecord;
use App\Models\TrainingRecord;
use App\Services\ReadinessService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class Readiness extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Personnel';

    protected static ?int $navigationSort = 11;

    protected string $view = 'filament.app.pages.readiness';

    protected ?string $subheading = 'How current each unit is on qualifications, credentials and attendance.';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user !== null
            && ($user->can('viewAny', QualificationRecord::class) || $user->can('viewAny', TrainingRecord::class));
    }

    /**
     * @return Collection<int, UnitReadinessData>
     */
    public function getSummaries(): Collection
    {
        return ReadinessService::summaries();
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Expiring In The Next 30 Days')
            ->records(fn (): array => ReadinessService::expiringItems()->all())
            ->emptyStateIcon(Heroicon::OutlinedShieldCheck)
            ->emptyStateHeading('Nothing Is Expiring Soon')
            ->emptyStateDescription('No qualifications or credentials expire in the next 30 days.')
            ->columns([
                TextColumn::make('member'),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Credential' ? 'info' : 'warning'),
                TextColumn::make('item')
                    ->label('Qualification Or Credential'),
                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->date()
                    ->description(fn (array $record): string => $record['expires_at']->diffForHumans()),
            ]);
    }

    /**
     * @return array<class-string>
     */
    protected function getFooterWidgets(): array
    {
        return [
            ExpirationsChart::class,
        ];
    }
}
