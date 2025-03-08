<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\PassportTokenResource\Widgets;

use App\Models\ApiLog;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class ApiRequests extends ChartWidget
{
    protected static ?string $heading = 'Live API Requests';

    protected static ?string $description = 'Your account\'s API request history over the last year.';

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
        $data = Trend::model(ApiLog::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'API requests',
                    'data' => $data->map(fn (TrendValue $value): mixed => $value->aggregate),
                    'fill' => true,
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value): string => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
