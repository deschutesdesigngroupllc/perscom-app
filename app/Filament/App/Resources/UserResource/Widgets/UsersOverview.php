<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class UsersOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -3;

    protected ?string $heading = 'New Users';

    /**
     * @return Stat[]
     */
    protected function getStats(): array
    {
        $dataMtd = Trend::model(User::class)
            ->between(
                start: now()->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perMonth()
            ->count();

        $dataQtd = Trend::model(User::class)
            ->between(
                start: now()->startOfQuarter(),
                end: now()->endOfQuarter(),
            )
            ->perMonth()
            ->count();

        $dataYtd = Trend::model(User::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            Stat::make('New Users MTD', User::query()->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count())
                ->chart($dataMtd->map(fn (TrendValue $value): mixed => $value->aggregate)->toArray())
                ->color('success'),
            Stat::make('New Users QTD', User::query()->whereBetween('created_at', [now()->startOfQuarter(), now()->endOfQuarter()])->count())
                ->chart($dataQtd->map(fn (TrendValue $value): mixed => $value->aggregate)->toArray())
                ->color('warning'),
            Stat::make('New Users YTD', User::query()->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()])->count())
                ->chart($dataYtd->map(fn (TrendValue $value): mixed => $value->aggregate)->toArray())
                ->color('danger'),
        ];
    }
}
