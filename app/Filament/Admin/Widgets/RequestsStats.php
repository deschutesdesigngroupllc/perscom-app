<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Metrics\ApiRequestMetric;
use App\Metrics\CliRequestMetric;
use App\Metrics\HttpRequestMetric;
use App\Metrics\Metric;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class RequestsStats extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $currentHttp = Metric::average(HttpRequestMetric::class);
        $previousHttp = Metric::average(HttpRequestMetric::class, function (Builder $query): void {
            $query->whereBetween('created_at', [now()->subMonths(2)->startOfMonth(), now()->subMonths(2)->endOfMonth()]);
        });
        $httpDiffPercentage = Number::percentageDifference($previousHttp, $currentHttp);
        $httpRequests = new HttpRequestMetric;
        $httpData = Trend::query($httpRequests->query())
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->sum('count');

        $currentApi = Metric::average(ApiRequestMetric::class);
        $previousApi = Metric::average(ApiRequestMetric::class, function (Builder $query): void {
            $query->whereBetween('created_at', [now()->subMonths(2)->startOfMonth(), now()->subMonths(2)->endOfMonth()]);
        });
        $apiDiffPercentage = Number::percentageDifference($previousApi, $currentApi);
        $apiRequests = new ApiRequestMetric;
        $apiData = Trend::query($apiRequests->query())
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->sum('count');

        $currentCli = Metric::average(CliRequestMetric::class);
        $previousCli = Metric::average(CliRequestMetric::class, function (Builder $query): void {
            $query->whereBetween('created_at', [now()->subMonths(2)->startOfMonth(), now()->subMonths(2)->endOfMonth()]);
        });
        $cliDiffPercentage = Number::percentageDifference($previousCli, $currentCli);
        $cliRequests = new CliRequestMetric;
        $cliData = Trend::query($cliRequests->query())
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->sum('count');

        $icon = fn ($number): string => match (true) {
            $number >= 0 => 'heroicon-m-arrow-trending-up',
            default => 'heroicon-m-arrow-trending-down'
        };

        return [
            Stat::make('Average HTTP requests', Number::format($currentHttp))
                ->color('info')
                ->description(Number::percentage($httpDiffPercentage))
                ->descriptionIcon($icon($httpDiffPercentage))
                ->chart($httpData->map(fn (TrendValue $value): mixed => $value->aggregate)->toArray()),
            Stat::make('Average API requests', $currentApi)
                ->color('success')
                ->description(Number::percentage($apiDiffPercentage))
                ->descriptionIcon($icon($apiDiffPercentage))
                ->chart($apiData->map(fn (TrendValue $value): mixed => $value->aggregate)->toArray()),
            Stat::make('Average CLI requests', $currentCli)
                ->color('danger')
                ->description(Number::percentage($cliDiffPercentage))
                ->descriptionIcon($icon($cliDiffPercentage))
                ->chart($cliData->map(fn (TrendValue $value): mixed => $value->aggregate)->toArray()),
        ];
    }
}
