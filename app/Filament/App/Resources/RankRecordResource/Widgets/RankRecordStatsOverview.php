<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\RankRecordResource\Widgets;

use App\Models\RankRecord;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class RankRecordStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $currentMtd = RankRecord::query()->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();
        $lastMtd = RankRecord::query()->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])->count();
        $percentageMtd = (($currentMtd - $lastMtd) / ($lastMtd === 0 ?: 1));

        $currentQtd = RankRecord::query()->whereBetween('created_at', [now()->startOfQuarter(), now()->endOfQuarter()])->count();
        $lastQtd = RankRecord::query()->whereBetween('created_at', [now()->subQuarter()->startOfQuarter(), now()->subQuarter()->endOfQuarter()])->count();
        $percentageQtd = (($currentQtd - $lastQtd) / ($lastQtd === 0 ?: 1));

        $currentYtd = RankRecord::query()->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()])->count();
        $lastYtd = RankRecord::query()->whereBetween('created_at', [now()->subYear()->startOfYear(), now()->subYear()->endOfYear()])->count();
        $percentageYtd = (($currentYtd - $lastYtd) / ($lastYtd === 0 ?: 1));

        return [
            Stat::make('Total Records MTD', (string) $currentMtd)
                ->description(Number::percentage($percentageMtd))
                ->descriptionIcon(match ($currentMtd <=> $lastMtd) {
                    1 => 'heroicon-m-arrow-trending-up',
                    -1 => 'heroicon-m-arrow-trending-down',
                    default => 'heroicon-m-arrows-right-left',
                })
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
                ->color(match ($currentYtd <=> $lastYtd) {
                    1 => 'success',
                    -1 => 'danger',
                    default => 'gray',
                }),
        ];
    }
}
