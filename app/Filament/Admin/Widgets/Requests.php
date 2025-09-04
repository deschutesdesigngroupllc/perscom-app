<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Metrics\ApiRequestMetric;
use App\Metrics\CliRequestMetric;
use App\Metrics\HttpRequestMetric;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class Requests extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Application Requests';

    protected ?string $description = 'The requests handled by the application.';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '150px';

    protected ?string $pollingInterval = '10s';

    protected ?array $options = [
        'plugins' => [
            'legend' => [
                'display' => false,
            ],
        ],
    ];

    protected function getData(): array
    {
        $httpRequests = new HttpRequestMetric;
        $httpData = Trend::query($httpRequests->query())
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->sum('count');

        $apiRequests = new ApiRequestMetric;
        $apiData = Trend::query($apiRequests->query())
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->sum('count');

        $cliRequests = new CliRequestMetric;
        $cliData = Trend::query($cliRequests->query())
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->sum('count');

        return [
            'datasets' => [
                [
                    'label' => 'HTTP requests',
                    'data' => $httpData->map(fn (TrendValue $value): mixed => $value->aggregate),
                    'fill' => true,
                    'borderColor' => '#2563eb',
                ],
                [
                    'label' => 'API requests',
                    'data' => $apiData->map(fn (TrendValue $value): mixed => $value->aggregate),
                    'fill' => false,
                    'borderColor' => '#166534',
                ],
                [
                    'label' => 'CLI requests',
                    'data' => $cliData->map(fn (TrendValue $value): mixed => $value->aggregate),
                    'fill' => false,
                    'borderColor' => '#991b1b',
                ],
            ],
            'labels' => $httpData->map(fn (TrendValue $value): string => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
