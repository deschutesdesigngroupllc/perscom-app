<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Metrics\JobFailedMetric;
use App\Metrics\JobProcessedMetric;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class JobsHandled extends ChartWidget
{
    protected static ?int $sort = 4;

    protected static ?string $heading = 'Jobs Handled';

    protected static ?string $description = 'The jobs processed and handled by the application.';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '150px';

    protected static ?string $pollingInterval = '10s';

    protected static ?array $options = [
        'plugins' => [
            'legend' => [
                'display' => false,
            ],
        ],
    ];

    protected function getData(): array
    {
        $jobsProcessed = new JobProcessedMetric;
        $jobsProcessedData = Trend::query($jobsProcessed->query())
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->sum('count');

        $jobsFailed = new JobFailedMetric;
        $jobsFailedData = Trend::query($jobsFailed->query())
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->sum('count');

        return [
            'datasets' => [
                [
                    'label' => 'Jobs processed',
                    'data' => $jobsProcessedData->map(fn (TrendValue $value) => $value->aggregate),
                    'fill' => true,
                    'borderColor' => '#2563eb',
                ],
                [
                    'label' => 'Jobs failed',
                    'data' => $jobsFailedData->map(fn (TrendValue $value) => $value->aggregate),
                    'fill' => true,
                    'borderColor' => '#991b1b',
                ],
            ],
            'labels' => $jobsProcessedData->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
