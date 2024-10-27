<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\WebhookResource\Widgets;

use App\Models\WebhookLog;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class WebhookRequests extends ChartWidget
{
    protected static ?string $heading = 'Live Webhook Requests';

    protected static ?string $description = 'Your account\'s webhook request history over the last year.';

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
        $data = Trend::model(WebhookLog::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Webhook requests',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'fill' => true,
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
