<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\ServiceRecordResource\Widgets;

use App\Models\ServiceRecord;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Number;

class ServiceRecordStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $currentMtd = ServiceRecord::query()->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();
        $lastMtd = ServiceRecord::query()->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])->count();
        $percentageMtd = (($currentMtd - $lastMtd) / ($lastMtd === 0 ?: 1));

        $currentQtd = ServiceRecord::query()->whereBetween('created_at', [now()->startOfQuarter(), now()->endOfQuarter()])->count();
        $lastQtd = ServiceRecord::query()->whereBetween('created_at', [now()->subQuarter()->startOfQuarter(), now()->subQuarter()->endOfQuarter()])->count();
        $percentageQtd = (($currentQtd - $lastQtd) / ($lastQtd === 0 ?: 1));

        $currentYtd = ServiceRecord::query()->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()])->count();
        $lastYtd = ServiceRecord::query()->whereBetween('created_at', [now()->subYear()->startOfYear(), now()->subYear()->endOfYear()])->count();
        $percentageYtd = (($currentYtd - $lastYtd) / ($lastYtd === 0 ?: 1));

        $mtd = Trend::model(ServiceRecord::class)
            ->between(
                start: now()->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perDay()
            ->count();

        $qtd = Trend::model(ServiceRecord::class)
            ->between(
                start: now()->startOfQuarter(),
                end: now()->endOfQuarter(),
            )
            ->perMonth()
            ->count();

        $ytd = Trend::model(ServiceRecord::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            Stat::make('Total Records MTD', (string) $currentMtd)
                ->description(Number::percentage($percentageMtd))
                ->descriptionIcon(match ($currentMtd <=> $lastMtd) {
                    1 => 'heroicon-m-arrow-trending-up',
                    -1 => 'heroicon-m-arrow-trending-down',
                    default => 'heroicon-m-arrows-right-left',
                })
                ->chart($mtd->map(fn (TrendValue $value): mixed => $value->aggregate)->toArray())
                ->color(match ($currentMtd <=> $lastMtd) {
                    1 => 'success',
                    -1 => 'danger',
                    default => 'gray',
                }),
            Stat::make('Total Records QTD', (string) $currentQtd)
                ->description(Number::percentage($percentageQtd))
                ->descriptionIcon(match ($currentQtd <=> $lastQtd) {
                    1 => 'heroicon-m-arrow-trending-up',
                    -1 => 'heroicon-m-arrow-trending-down',
                    default => 'heroicon-m-arrows-right-left',
                })
                ->chart($qtd->map(fn (TrendValue $value): mixed => $value->aggregate)->toArray())
                ->color(match ($currentQtd <=> $lastQtd) {
                    1 => 'success',
                    -1 => 'danger',
                    default => 'gray',
                }),
            Stat::make('Total Records YTD', (string) $currentYtd)
                ->description(Number::percentage($percentageYtd))
                ->descriptionIcon(match ($currentYtd <=> $lastYtd) {
                    1 => 'heroicon-m-arrow-trending-up',
                    -1 => 'heroicon-m-arrow-trending-down',
                    default => 'heroicon-m-arrows-right-left',
                })
                ->chart($ytd->map(fn (TrendValue $value): mixed => $value->aggregate)->toArray())
                ->color(match ($currentYtd <=> $lastYtd) {
                    1 => 'success',
                    -1 => 'danger',
                    default => 'gray',
                }),
        ];
    }
}
